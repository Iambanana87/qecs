<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\Http\Resources\ComplaintListResource;
use App\DTOs\ComplaintDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    private function getBaseQuery()
    {
        return DB::table('complaints')
            ->leftJoin('customers', 'complaints.customer_id', '=', 'customers.id')
            ->leftJoin('partners', 'complaints.partner_id', '=', 'partners.id')
            ->select(
                'complaints.*',
                'customers.name as cust_name',
                'customers.department as cust_department',
                'customers.department_manager as cust_manager',
                'customers.line_area as cust_line_area',
                'partners.name as part_name',
                'partners.country as part_country',
                'partners.code as part_code',
                'partners.contact as part_contact'
            )
            ->whereNull('complaints.deleted_at');
    }

    public function index(Request $request)
    {
        $query = $this->getBaseQuery();

        if ($request->has('type')) {
            $query->where('complaints.type', $request->query('type'));
        }
        $paginator = $query->orderBy('complaints.created_at', 'desc')->paginate(10);
        
        $paginator->getCollection()->transform(function ($row) {
            return ComplaintDTO::fromDb($row);
        });

        // Resource sẽ format từng item theo cấu trúc mới (id + general con)
        $resource = ComplaintListResource::collection($paginator);

        $response = $resource->response()->getData(true);

        // Trả về JSON
        return response()->json([
            'data' => $response['data'],  
            'links'   => $response['links'],
            'meta'    => $response['meta'],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->getBaseQuery()->where('complaints.id', $id)->first();
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        return response()->json([
            // 'success' => true,
            'data' => new ComplaintResource(ComplaintDTO::fromDb($row)),
        ]);
    }

    public function store(StoreComplaintRequest $request): JsonResponse
    {
        $data = $request->validated();
        $uuid = (string) Str::uuid();

        $photosJson = isset($data['photos']) ? json_encode($data['photos']) : null;
        $partnerPhotosJson = isset($data['partner_photos']) ? json_encode($data['partner_photos']) : null;
        $floorProcessJson = isset($data['floor_process_visualization']) 
            ? json_encode($data['floor_process_visualization']) 
            : null;

        $insertData = [
            'id' => $uuid,
            'type' => $data['type'],
            'complaint_no' => $data['complaint_no'] ?? null,
            'subject' => $data['subject'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'partner_id' => $data['partner_id'] ?? null,
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
            'date_occurrence' => $data['problem_occurrence'] ?? null,
            'date_detection' => $data['problem_detection'] ?? null,
            'date_report' => $data['report_time'] ?? null,
            'product_description' => $data['product_description'] ?? null,
            'detection_point' => $data['detection_point'] ?? null,
            'photo' => $photosJson,          
            'attachment' => $partnerPhotosJson, 
            'floor_process_visualization' => $floorProcessJson,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('complaints')->insert($insertData);
        $row = $this->getBaseQuery()->where('complaints.id', $uuid)->first();
        return response()->json([
            'success' => true,
            'data' => new ComplaintResource(ComplaintDTO::fromDb($row)),
        ], 201);
    }
}