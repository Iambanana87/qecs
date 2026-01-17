<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreCheckMaterialMachineRequest;
use App\Http\Requests\UpdateCheckMaterialMachineRequest;
use App\Http\Resources\CheckMaterialMachineResource;
use App\DTOs\CheckMaterialMachineDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckMaterialMachineController extends Controller
{
    //index
    public function index(Request $request)
    {
        $query = DB::table('check_material_machines')->whereNull('deleted_at');

        // Filter bắt buộc hoặc tùy chọn: Lấy các check list của 1 complaint cụ thể
        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }

        $paginator = $query->orderBy('no', 'asc') 
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        // Transform stdClass -> DTO
        $paginator->getCollection()->transform(function ($row) {
            return CheckMaterialMachineDTO::fromDb($row);
        });

        return CheckMaterialMachineResource::collection($paginator);
    }

    // show
    public function show(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CheckMaterialMachineResource(CheckMaterialMachineDTO::fromDb($row)),
        ]);
    }

    // store
    public function store(StoreCheckMaterialMachineRequest $request): JsonResponse
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
            
            // Encode JSON
            'before_photo' => isset($data['before_photo']) ? json_encode($data['before_photo']) : null,
            'after_photo' => isset($data['after_photo']) ? json_encode($data['after_photo']) : null,
            
            'respons' => $data['respons'] ?? null,
            'control_frequency' => $data['control_frequency'] ?? null,
            'status' => $data['status'] ?? null,
            'close_date' => $data['close_date'] ?? null,
            
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('check_material_machines')->insert($insertData);

        // Fetch lại để trả về chuẩn DTO
        $row = $this->findRow($uuid);

        return response()->json([
            'success' => true,
            'data' => new CheckMaterialMachineResource(CheckMaterialMachineDTO::fromDb($row)),
        ], 201);
    }

    // update
    public function update(UpdateCheckMaterialMachineRequest $request, string $id): JsonResponse
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

        // Các trường ảnh (array -> json)
        if (array_key_exists('before_photo', $data)) {
            $updateData['before_photo'] = !empty($data['before_photo']) ? json_encode($data['before_photo']) : null;
        }
        if (array_key_exists('after_photo', $data)) {
            $updateData['after_photo'] = !empty($data['after_photo']) ? json_encode($data['after_photo']) : null;
        }

        DB::table('check_material_machines')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new CheckMaterialMachineResource(CheckMaterialMachineDTO::fromDb($this->findRow($id))),
        ]);
    }

    // ---DESTROY: Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('check_material_machines')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::table('check_material_machines')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // --- Helper ---
    private function findRow(string $id)
    {
        return DB::table('check_material_machines')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }
}