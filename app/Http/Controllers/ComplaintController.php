<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\DTOs\ComplaintDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    // --- 1. INDEX: Lấy danh sách (kèm Join) ---
    public function index(Request $request)
    {
        // Query cơ bản
        $query = DB::table('complaints')
            ->leftJoin('customers', 'complaints.customer_id', '=', 'customers.id')
            ->leftJoin('partners', 'complaints.partner_id', '=', 'partners.id')
            ->select(
                'complaints.*', 
                'customers.name as customer_name', 
                'partners.name as partner_name'   
            )
            ->whereNull('complaints.deleted_at');

        // Filter cơ bản
        if ($request->has('type')) {
            $query->where('complaints.type', 'like', '%' . $request->query('type') . '%');
        }
        if ($request->has('customer_id')) {
            $query->where('complaints.customer_id', $request->query('customer_id'));
        }

        $paginator = $query->orderBy('complaints.created_at', 'desc')->paginate(10);

        // Transform
        $paginator->getCollection()->transform(function ($row) {
            return ComplaintDTO::fromDb($row);
        });

        return ComplaintResource::collection($paginator);
    }

    // --- 2. SHOW: Xem chi tiết ---
    public function show(string $id): JsonResponse
    {
        $row = $this->findRowWithRelations($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ComplaintResource(ComplaintDTO::fromDb($row)),
        ]);
    }

    // --- 3. STORE: Tạo mới ---
    public function store(StoreComplaintRequest $request): JsonResponse
    {
        $data = $request->validated();
        $uuid = (string) Str::uuid();

        // Map request data sang DB columns
        $insertData = [
            'id' => $uuid,
            'type' => $data['type'],
            'complaint_no' => $data['complaint_no'] ?? null,
            'subject' => $data['subject'] ?? null,
            
            'customer_id' => $data['customer_id'] ?? null,
            'partner_id' => $data['partner_id'] ?? null,
            'five_why_id' => null, // Mặc định null, sẽ cập nhật khi tạo FiveWhy sau
            
            'incident_type' => $data['incident_type'] ?? null,
            'category' => $data['category'] ?? null,
            'severity_level' => $data['severity_level'] ?? null,
            'machine' => $data['machine'] ?? null,
            'report_completed_by' => $data['report_completed_by'] ?? null,
            
            'lot_code' => $data['lot_code'] ?? null,
            'product_code' => $data['product_code'] ?? null,
            'unit_qty_audited' => $data['unit_qty_audited'] ?? null,
            'unit_qty_rejected' => $data['unit_qty_rejected'] ?? null,
            'date_code' => $data['date_code'] ?? null,
            
            'date_occurrence' => $data['date_occurrence'] ?? null,
            'date_detection' => $data['date_detection'] ?? null,
            'date_report' => $data['date_report'] ?? null,
            
            'product_description' => $data['product_description'] ?? null,
            'detection_point' => $data['detection_point'] ?? null,
            'photo' => $data['photo'] ?? null,
            'detection_method' => $data['detection_method'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            
            // Encode JSON
            'floor_process_visualization' => isset($data['floor_process_visualization']) 
                ? json_encode($data['floor_process_visualization']) 
                : null,
            
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('complaints')->insert($insertData);

        // Fetch lại kèm relations
        $row = $this->findRowWithRelations($uuid);

        return response()->json([
            'success' => true,
            'data' => new ComplaintResource(ComplaintDTO::fromDb($row)),
        ], 201);
    }

    // --- 4. UPDATE: Cập nhật ---
    public function update(UpdateComplaintRequest $request, string $id): JsonResponse
    {
        $existing = DB::table('complaints')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$existing) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validated();
        $updateData = ['updated_at' => now()];

        // List các cột được phép update
        $fields = [
            'type', 'complaint_no', 'subject', 'customer_id', 'partner_id',
            'incident_type', 'category', 'severity_level', 'machine', 'report_completed_by',
            'lot_code', 'product_code', 'unit_qty_audited', 'unit_qty_rejected', 'date_code',
            'date_occurrence', 'date_detection', 'date_report',
            'product_description', 'detection_point', 'photo', 'detection_method', 'attachment'
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        // Xử lý riêng JSON
        if (array_key_exists('floor_process_visualization', $data)) {
            $updateData['floor_process_visualization'] = !empty($data['floor_process_visualization']) 
                ? json_encode($data['floor_process_visualization']) 
                : null;
        }

        DB::table('complaints')->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new ComplaintResource(ComplaintDTO::fromDb($this->findRowWithRelations($id))),
        ]);
    }

    // --- 5. DESTROY: Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('complaints')->where('id', $id)->whereNull('deleted_at')->exists();
        if (!$exists) return response()->json(['message' => 'Not found'], 404);

        DB::table('complaints')->where('id', $id)->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // --- Helper: Find Row with Joins ---
    private function findRowWithRelations(string $id)
    {
        return DB::table('complaints')
            ->leftJoin('customers', 'complaints.customer_id', '=', 'customers.id')
            ->leftJoin('partners', 'complaints.partner_id', '=', 'partners.id')
            ->select(
                'complaints.*', 
                'customers.name as customer_name', 
                'partners.name as partner_name'
            )
            ->where('complaints.id', $id)
            ->whereNull('complaints.deleted_at')
            ->first();
    }
}