<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FishboneController extends Controller
{
    protected string $table = 'rca_fishbones';

    /**
     * Lấy dữ liệu Fishbone (Nhóm theo Category để vẽ biểu đồ)
     * GET /?c=Fishbone&m=index&complaint_id={uuid}
     */
    public function index(Request $request): JsonResponse
    {
        $complaintId = $request->query('complaint_id');
        if (!$complaintId) return response()->json(['message' => 'complaint_id is required'], 422);

        $data = DB::table($this->table)
            ->where('complaint_id', $complaintId)
            ->whereNull('deleted_at')
            ->get();

        // Group dữ liệu theo category (Man, Machine...)
        // Kết quả trả về sẽ dạng: {"Man": [...], "Machine": [...]}
        // Rất tiện cho Frontend mapping vào các nhánh xương cá
        $grouped = $data->groupBy('category');

        return response()->json($grouped);
    }

    /**
     * Thêm một nguyên nhân vào nhánh xương cá
     * POST /?c=Fishbone&m=store
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'complaint_id'  => 'required|exists:complaints,id',
            'category'      => 'required|in:Man,Machine,Method,Material,Environment,Measurement',
            'cause_detail'  => 'required|string|max:255',
            'is_root_cause' => 'boolean' // Checkbox: Đây có phải nguyên nhân gốc không?
        ]);

        $id = Str::uuid()->toString();
        $now = Carbon::now();

        DB::table($this->table)->insert([
            'id'            => $id,
            'complaint_id'  => $request->input('complaint_id'),
            'category'      => $request->input('category'),
            'cause_detail'  => $request->input('cause_detail'),
            'is_root_cause' => $request->boolean('is_root_cause', false),
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        return response()->json(['message' => 'Cause added successfully', 'id' => $id]);
    }

    /**
     * Cập nhật nguyên nhân (Sửa nội dung hoặc đánh dấu Root Cause)
     * PUT /?c=Fishbone&m=update&id={uuid}
     */
    public function update(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (!$id) return response()->json(['message' => 'id is required'], 422);

        $exists = DB::table($this->table)->where('id', $id)->whereNull('deleted_at')->exists();
        if (!$exists) return response()->json(['message' => 'Record not found'], 404);

        $request->validate([
            'category'      => 'sometimes|in:Man,Machine,Method,Material,Environment,Measurement',
            'cause_detail'  => 'sometimes|string|max:255',
            'is_root_cause' => 'sometimes|boolean'
        ]);

        $data = $request->only(['category', 'cause_detail', 'is_root_cause']);
        $data['updated_at'] = Carbon::now();

        DB::table($this->table)->where('id', $id)->update($data);

        return response()->json(['message' => 'Updated successfully']);
    }

    /**
     * Xóa nguyên nhân khỏi biểu đồ
     * DELETE /?c=Fishbone&m=destroy&id={uuid}
     */
    public function destroy(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (!$id) return response()->json(['message' => 'id is required'], 422);

        DB::table($this->table)->where('id', $id)->update(['deleted_at' => Carbon::now()]);

        return response()->json(['message' => 'Deleted successfully']);
    }
}