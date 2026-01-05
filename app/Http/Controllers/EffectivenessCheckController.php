<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEffectivenessCheckRequest;
use App\Http\Requests\UpdateEffectivenessCheckRequest;
use App\Http\Resources\EffectivenessCheckResource;
use App\DTOs\EffectivenessCheckDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EffectivenessCheckController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('effectiveness_checks')->whereNull('deleted_at');

        // Filter theo complaint_id
        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }

        // Filter theo loại nguyên nhân 
        if ($request->has('produce_cause')) {
            $val = filter_var($request->query('produce_cause'), FILTER_VALIDATE_BOOLEAN);
            $query->where('produce_cause', $val);
        }

        $paginator = $query->orderBy('no', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        $paginator->getCollection()->transform(function ($row) {
            return EffectivenessCheckDTO::fromDb($row);
        });

        return EffectivenessCheckResource::collection($paginator);
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EffectivenessCheckResource(EffectivenessCheckDTO::fromDb($row)),
        ]);
    }

    public function store(StoreEffectivenessCheckRequest $request): JsonResponse
    {
        $data = $request->validated();
        $uuid = (string) Str::uuid();

        $insertData = [
            'id' => $uuid,
            'complaint_id' => $data['complaint_id'],
            'produce_cause' => $data['produce_cause'] ?? false,
            'no' => $data['no'] ?? null,
            'action' => $data['action'] ?? null,
            'responsible' => $data['responsible'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'verification' => $data['verification'] ?? false,
            
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('effectiveness_checks')->insert($insertData);

        $row = $this->findRow($uuid);

        return response()->json([
            'success' => true,
            'data' => new EffectivenessCheckResource(EffectivenessCheckDTO::fromDb($row)),
        ], 201);
    }

    public function update(UpdateEffectivenessCheckRequest $request, string $id): JsonResponse
    {
        $row = $this->findRow($id);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validated();
        $updateData = ['updated_at' => now()];

        // Các field cho phép update
        $fields = ['produce_cause', 'no', 'action', 'responsible', 'end_date', 'verification'];
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        DB::table('effectiveness_checks')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new EffectivenessCheckResource(EffectivenessCheckDTO::fromDb($this->findRow($id))),
        ]);
    }

    // --- Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('effectiveness_checks')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::table('effectiveness_checks')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // --- Helper ---
    private function findRow(string $id)
    {
        return DB::table('effectiveness_checks')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }
}