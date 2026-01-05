<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCheckParametersOperationRequest;
use App\Http\Requests\UpdateCheckParametersOperationRequest;
use App\Http\Resources\CheckParametersOperationResource;
use App\DTOs\CheckParametersOperationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckParametersOperationController extends Controller
{
    // --- 1. INDEX: Lấy danh sách ---
    public function index(Request $request)
    {
        $query = DB::table('check_parameters_operations')->whereNull('deleted_at');

        // Filter theo complaint_id
        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }

        $paginator = $query->orderBy('no', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        // Transform stdClass -> DTO
        $paginator->getCollection()->transform(function ($row) {
            return CheckParametersOperationDTO::fromDb($row);
        });

        return CheckParametersOperationResource::collection($paginator);
    }

    // --- 2. SHOW: Xem chi tiết ---
    public function show(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CheckParametersOperationResource(CheckParametersOperationDTO::fromDb($row)),
        ]);
    }

    // --- 3. STORE: Tạo mới ---
    public function store(StoreCheckParametersOperationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $uuid = (string) Str::uuid();

        // Prepare data
        $insertData = [
            'id' => $uuid,
            'complaint_id' => $data['complaint_id'],
            'no' => $data['no'] ?? null,
            'machine' => $data['machine'] ?? null,
            'sub_assembly' => $data['sub_assembly'] ?? null,
            'component' => $data['component'] ?? null,
            'description' => $data['description'] ?? null,
            'current_condition' => $data['current_condition'] ?? null,
            
            // Encode JSON mảng ảnh
            'before_photo' => isset($data['before_photo']) ? json_encode($data['before_photo']) : null,
            'after_photo' => isset($data['after_photo']) ? json_encode($data['after_photo']) : null,
            
            'respons' => $data['respons'] ?? null,
            'control_frequency' => $data['control_frequency'] ?? null,
            'status' => $data['status'] ?? null,
            'close_date' => $data['close_date'] ?? null,
            
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('check_parameters_operations')->insert($insertData);

        $row = $this->findRow($uuid);

        return response()->json([
            'success' => true,
            'data' => new CheckParametersOperationResource(CheckParametersOperationDTO::fromDb($row)),
        ], 201);
    }

    // --- 4. UPDATE: Cập nhật ---
    public function update(UpdateCheckParametersOperationRequest $request, string $id): JsonResponse
    {
        $row = $this->findRow($id);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validated();
        $updateData = ['updated_at' => now()];

        // Các trường text/số
        $fields = [
            'no', 'machine', 'sub_assembly', 'component', 'description', 
            'current_condition', 'respons', 'control_frequency', 'status', 'close_date'
        ];
        
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        // Các trường ảnh
        if (array_key_exists('before_photo', $data)) {
            $updateData['before_photo'] = !empty($data['before_photo']) ? json_encode($data['before_photo']) : null;
        }
        if (array_key_exists('after_photo', $data)) {
            $updateData['after_photo'] = !empty($data['after_photo']) ? json_encode($data['after_photo']) : null;
        }

        DB::table('check_parameters_operations')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new CheckParametersOperationResource(CheckParametersOperationDTO::fromDb($this->findRow($id))),
        ]);
    }

    // --- 5. DESTROY: Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('check_parameters_operations')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::table('check_parameters_operations')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // --- Helper ---
    private function findRow(string $id)
    {
        return DB::table('check_parameters_operations')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }
}