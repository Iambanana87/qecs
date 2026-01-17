<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImmediateActionRequest;
use App\Http\Requests\UpdateImmediateActionRequest;
use App\Http\Resources\ImmediateActionResource;
use App\DTOs\ImmediateActionDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImmediateActionController extends Controller
{
    
    public function index(Request $request)
    {
        $query = DB::table('immediate_actions')->whereNull('deleted_at');
        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }

        $paginator = $query->orderBy('no', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        $paginator->getCollection()->transform(function ($row) {
            return ImmediateActionDTO::fromDb($row);
        });

        return ImmediateActionResource::collection($paginator);
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ImmediateActionResource(ImmediateActionDTO::fromDb($row)),
        ]);
    }

    public function store(StoreImmediateActionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $uuid = (string) Str::uuid();

        $insertData = [
            'id' => $uuid,
            'complaint_id' => $data['complaint_id'],
            'no' => $data['no'] ?? null,
            'action' => $data['action'] ?? null,
            'status' => $data['status'] ?? null,
            'responsible' => $data['responsible'] ?? null,
            
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('immediate_actions')->insert($insertData);

        $row = $this->findRow($uuid);

        return response()->json([
            'success' => true,
            'data' => new ImmediateActionResource(ImmediateActionDTO::fromDb($row)),
        ], 201);
    }

    public function update(UpdateImmediateActionRequest $request, string $id): JsonResponse
    {
        $row = $this->findRow($id);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validated();
        $updateData = ['updated_at' => now()];

        // Các trường cần update
        $fields = ['no', 'action', 'status', 'responsible'];
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        DB::table('immediate_actions')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new ImmediateActionResource(ImmediateActionDTO::fromDb($this->findRow($id))),
        ]);
    }

    // --- 5. DESTROY: Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('immediate_actions')
            ->where('id', $id)
            ->whereNull('deleted_at') // Đảm bảo chưa xóa
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::table('immediate_actions')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // --- Helper ---
    private function findRow(string $id)
    {
        return DB::table('immediate_actions')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }
}