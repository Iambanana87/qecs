<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCorrectiveActionRequest;
use App\Http\Requests\UpdateCorrectiveActionRequest;
use App\Http\Resources\CorrectiveActionResource;
use App\DTOs\CorrectiveActionDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CorrectiveActionController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('corrective_actions')->whereNull('deleted_at');

        // Filter theo complaint_id
        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }

        // Sắp xếp theo số thứ tự (no)
        $paginator = $query->orderBy('no', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        // Transform stdClass -> DTO
        $paginator->getCollection()->transform(function ($row) {
            return CorrectiveActionDTO::fromDb($row);
        });

        return CorrectiveActionResource::collection($paginator);
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CorrectiveActionResource(CorrectiveActionDTO::fromDb($row)),
        ]);
    }

    public function store(StoreCorrectiveActionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $uuid = (string) Str::uuid();

        $insertData = [
            'id' => $uuid,
            'complaint_id' => $data['complaint_id'],
            'no' => $data['no'] ?? null,
            'action' => $data['action'] ?? null,
            'responsible' => $data['responsible'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'verification' => $data['verification'] ?? false, 
            
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('corrective_actions')->insert($insertData);

        // Lấy lại dữ liệu
        $row = $this->findRow($uuid);

        return response()->json([
            'success' => true,
            'data' => new CorrectiveActionResource(CorrectiveActionDTO::fromDb($row)),
        ], 201);
    }

    public function update(UpdateCorrectiveActionRequest $request, string $id): JsonResponse
    {
        $row = $this->findRow($id);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validated();
        $updateData = ['updated_at' => now()];

        // Danh sách các trường có thể update
        $fields = ['no', 'action', 'responsible', 'end_date', 'verification'];
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        DB::table('corrective_actions')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new CorrectiveActionResource(CorrectiveActionDTO::fromDb($this->findRow($id))),
        ]);
    }

    // ---  Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('corrective_actions')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::table('corrective_actions')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // --- Helper ---
    private function findRow(string $id)
    {
        return DB::table('corrective_actions')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }
}