<?php

namespace App\Http\Controllers;

use App\Models\ContainmentAction; // Import Model vừa tạo
use Illuminate\Http\Request;

class ContainmentActionController extends Controller
{
    /**
     * Tạo mới Action
     * POST /?c=ContainmentAction&m=store
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $data = $request->validate([
            'complaint_id'     => 'required|exists:complaints,id',
            'action_content'   => 'required|string',
            'person_in_charge' => 'nullable|string|max:36',
            'due_date'         => 'nullable|date',
        ]);

        // 2. Gán giá trị mặc định
        $data['status'] = 'Pending';

        // 3. Tạo mới bằng Eloquent
        // - Không cần Str::uuid(), Model tự sinh ID.
        // - Không cần created_at/updated_at, Model tự điền.
        $action = ContainmentAction::create($data);

        return response()->json(['message' => 'Created', 'id' => $action->id]);
    }

    /**
     * Cập nhật Action
     * POST /?c=ContainmentAction&m=update&id={uuid}
     */
    public function update(Request $request)
    {
        $id = $request->query('id');

        // 1. Tìm bản ghi (Tự động trả về lỗi 404 nếu không thấy)
        $action = ContainmentAction::findOrFail($id);

        // 2. Validate dữ liệu cần update
        $request->validate([
            'action_content'   => 'sometimes|string',
            'person_in_charge' => 'sometimes|string|max:36',
            'due_date'         => 'sometimes|date',
            'status'           => 'sometimes|in:Pending,In_Progress,Completed',
            'completion_date'  => 'nullable|date'
        ]);

        // 3. Update (Chỉ lấy các trường hợp lệ)
        $action->update($request->only([
            'action_content', 
            'person_in_charge', 
            'due_date', 
            'status', 
            'completion_date'
        ]));
        // updated_at sẽ tự động nhảy thời gian hiện tại

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Xóa mềm Action
     * POST /?c=ContainmentAction&m=destroy&id={uuid}
     */
    public function destroy(Request $request)
    {
        $id = $request->query('id');

        // 1. Tìm bản ghi
        $action = ContainmentAction::findOrFail($id);

        // 2. Xóa mềm (Eloquent sẽ tự cập nhật cột deleted_at)
        $action->delete();

        return response()->json(['message' => 'Deleted']);
    }
}