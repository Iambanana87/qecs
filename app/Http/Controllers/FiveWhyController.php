<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FiveWhyController extends Controller
{
    protected string $table = 'rca_five_whys';

    /**
     * Lấy danh sách 5 Why của một Complaint
     * GET /?c=FiveWhy&m=index&complaint_id={uuid}
     */
    public function index(Request $request): JsonResponse
    {
        $complaintId = $request->query('complaint_id');
        if (!$complaintId) return response()->json(['message' => 'complaint_id is required'], 422);

        $data = DB::table($this->table)
            ->where('complaint_id', $complaintId)
            ->whereNull('deleted_at')
            ->orderBy('iteration', 'asc') // Sắp xếp theo thứ tự câu hỏi 1->5
            ->get();

        return response()->json($data);
    }

    /**
     * Thêm mới hoặc Cập nhật 1 dòng Why
     * POST /?c=FiveWhy&m=store
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validate
        $request->validate([
            'complaint_id' => 'required|exists:complaints,id',
            'iteration'    => 'required|integer|min:1|max:5', // Chỉ cho phép từ 1 đến 5
            'question'     => 'nullable|string',
            'answer'       => 'required|string',
        ]);

        // 2. Logic: Một complaint chỉ có tối đa 1 dòng cho mỗi iteration (vd: chỉ có 1 câu Why số 3)
        // Nên ta kiểm tra nếu đã có thì Update, chưa có thì Insert (Upsert)
        
        $existing = DB::table($this->table)
            ->where('complaint_id', $request->complaint_id)
            ->where('iteration', $request->iteration)
            ->whereNull('deleted_at')
            ->first();

        $now = Carbon::now();

        if ($existing) {
            // Update
            DB::table($this->table)
                ->where('id', $existing->id)
                ->update([
                    'question'   => $request->question,
                    'answer'     => $request->answer,
                    'updated_at' => $now
                ]);
            return response()->json(['message' => 'Updated successfully', 'id' => $existing->id]);
        } else {
            // Insert
            $id = Str::uuid()->toString();
            DB::table($this->table)->insert([
                'id'           => $id,
                'complaint_id' => $request->complaint_id,
                'iteration'    => $request->iteration,
                'question'     => $request->question,
                'answer'       => $request->answer,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            return response()->json(['message' => 'Created successfully', 'id' => $id]);
        }
    }

    /**
     * Xóa 1 dòng Why
     * DELETE /?c=FiveWhy&m=destroy&id={uuid}
     */
    public function destroy(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (!$id) return response()->json(['message' => 'id is required'], 422);

        DB::table($this->table)->where('id', $id)->update(['deleted_at' => Carbon::now()]);

        return response()->json(['message' => 'Deleted successfully']);
    }
}