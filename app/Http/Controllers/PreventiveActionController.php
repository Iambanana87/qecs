<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePreventiveActionRequest;
use App\Http\Requests\UpdatePreventiveActionRequest;
use App\Http\Resources\PreventiveActionResource;
use App\DTOs\PreventiveActionDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PreventiveActionController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('preventive_actions')->whereNull('deleted_at');

        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }

        $paginator = $query->orderBy('no', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        $paginator->getCollection()->transform(function ($row) {
            return PreventiveActionDTO::fromDb($row);
        });

        return PreventiveActionResource::collection($paginator);
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PreventiveActionResource(PreventiveActionDTO::fromDb($row)),
        ]);
    }

    public function store(StorePreventiveActionRequest $request): JsonResponse
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
            
            'complaint_responsible' => $data['complaint_responsible'] ?? null,
            'production_representative' => $data['production_representative'] ?? null,
            'quality_representative' => $data['quality_representative'] ?? null,
            'engineering_representative' => $data['engineering_representative'] ?? null,
            'quality_manager' => $data['quality_manager'] ?? null,
            
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('preventive_actions')->insert($insertData);

        $row = $this->findRow($uuid);

        return response()->json([
            'success' => true,
            'data' => new PreventiveActionResource(PreventiveActionDTO::fromDb($row)),
        ], 201);
    }

    public function update(UpdatePreventiveActionRequest $request, string $id): JsonResponse
    {
        $row = $this->findRow($id);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validated();
        $updateData = ['updated_at' => now()];

        $fields = [
            'no', 'action', 'responsible', 'end_date', 'verification',
            'complaint_responsible', 'production_representative', 
            'quality_representative', 'engineering_representative', 'quality_manager'
        ];
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        DB::table('preventive_actions')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new PreventiveActionResource(PreventiveActionDTO::fromDb($this->findRow($id))),
        ]);
    }

    // --- Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('preventive_actions')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::table('preventive_actions')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // --- Helper ---
    private function findRow(string $id)
    {
        return DB::table('preventive_actions')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }
}