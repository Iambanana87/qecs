<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ComplaintController extends Controller
{
    protected string $table = 'complaints'; 
    protected array $searchableLike = ['description', 'report_number', 'product_name', 'lot_number', 'defect_location'];
    protected array $filterableEq  = [
        'status', 'customer_id', 'created_by', 'product_name'
    ];
    protected array $sortable = [
        'created_at', 'updated_at', 'status', 'report_number', 'defect_quantity'
    ];

    //========================= CRUD Methods ========================= //
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min((int) $request->query('per_page', 15), 100));

        $query = DB::connection('mysql')
            ->table($this->table . ' as c')
            ->leftJoin('User as u', 'u.id', '=', 'c.created_by');

        $query->select([
            'c.*',
            'u.name as created_by_name',
            'u.email as created_by_email',
        ]);
        // Only show non-deleted records
        if (Schema::hasColumn($this->table, 'deleted_at')) {
            $query->whereNull('c.deleted_at');
        }

        // Filter
        foreach ($this->filterableEq as $col) {
            if ($request->filled($col)) {
                $query->where('c.' . $col, $request->input($col));
            }
        }

        // Search Keyword
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                foreach ($this->searchableLike as $col) {
                    $q->orWhere('c.' . $col, 'LIKE', "%{$keyword}%");
                }
            });
        }

        // Sort
        $sort = $request->query('sort', '-created_at'); 
        [$sortCol, $sortDir] = $this->parseSort($sort);
        
        if (Schema::hasColumn($this->table, $sortCol)) {
             $query->orderBy('c.' . $sortCol, $sortDir);
        } else {
             $query->orderBy('c.created_at', 'desc');
        }

        $data = $query->paginate($perPage);
        return response()->json($data, 200);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['message' => 'Method Not Allowed. Use POST.'], 405);
        }

        // Validate
        $request->validate([
            'product_name'    => ['required', 'string', 'max:255'],
            'lot_number'      => ['nullable', 'string', 'max:100'],
            'mfg_date'        => ['nullable', 'date'],
            'exp_date'        => ['nullable', 'date'],
            'total_quantity'  => ['nullable', 'numeric'],
            'defect_quantity' => ['nullable', 'numeric'],
            'description'     => ['nullable', 'string'],
            'defect_location' => ['nullable', 'string'],
            'customer_id'     => ['nullable', 'string', 'max:36'],

        ]);

        $data = $request->all();
        $now  = Carbon::now();
        $data['id'] = (string) Str::uuid();

        DB::connection('mysql')->transaction(function () use (&$data) {
            $year = Carbon::now()->year;
            $prefix = "COM-{$year}-";


            $maxRef = DB::table($this->table)
                ->where('report_number', 'LIKE', "{$prefix}%")
                ->lockForUpdate() 
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(report_number, '-', -1) AS UNSIGNED)) as max_num")
                ->value('max_num');

            $nextNum = ($maxRef ?? 0) + 1;
            $data['report_number'] = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        });

        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $data['status'] = 'PLAN';
        $data['created_by'] = $data['created_by'] ?? $request->user()?->id; 

        $insert = collect($data)->filter(function ($value, $key) {
            return Schema::connection('mysql')->hasColumn($this->table, $key);
        })->all();

        DB::table($this->table)->insert($insert);

        return $this->showWithJoin($data['id']);
    }

    public function show(Request $request): JsonResponse
    {
        $id = $request->query('id'); 
        if (!$id) {
            return response()->json(['message' => 'id is required'], 422);
        }
        return $this->showWithJoin($id);
    }

    public function update(Request $request): JsonResponse
    {
        if (!$request->isMethod('put') && !$request->isMethod('patch')) {
            return response()->json(['message' => 'Method Not Allowed. Use PUT/PATCH.'], 405);
        }

        $id = $request->query('id');
        if (!$id) return response()->json(['message' => 'id is required'], 422);

        $exists = DB::table($this->table)->where('id', $id)->whereNull('deleted_at')->exists();
        if (!$exists) return response()->json(['message' => 'Complaint not found'], 404);

        $data = $request->all();
        $data['updated_at'] = Carbon::now();

        $allowedColumns = Schema::getColumnListing($this->table);
        $update = collect($data)
            ->only($allowedColumns)
            ->except(['id', 'report_number', 'created_at', 'created_by', 'deleted_at']) 
            ->all();

        if (!empty($update)) {
            DB::table($this->table)->where('id', $id)->update($update);
        }

        return $this->showWithJoin($id);
    }

    
    public function destroy(Request $request): JsonResponse
    {
        if (!$request->isMethod('delete')) {
            return response()->json(['message' => 'Method Not Allowed. Use DELETE.'], 405);
        }

        $id = $request->query('id');
        if (!$id) return response()->json(['message' => 'id is required'], 422);

        $record = DB::table($this->table)->where('id', $id)->whereNull('deleted_at')->first();
        if (!$record) return response()->json(['message' => 'Complaint not found'], 404);
        
        DB::table($this->table)->where('id', $id)->update(['deleted_at' => Carbon::now()]);

        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    /* ========================= Helpers ========================= */

    protected function showWithJoin(string $id): JsonResponse
    {
        // Main Complaint with Created By info
        $complaint = DB::connection('mysql')
            ->table($this->table . ' as c')
            ->leftJoin('User as u', 'u.id', '=', 'c.created_by')
            ->where('c.id', $id)
            ->select([
                'c.*',
                'u.name as created_by_name',
                'u.avatar_url as created_by_avatar'
            ])
            ->first();

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], 404);
        }

        // Containment Actions (Plan)
        $complaint->containment_actions = DB::table('containment_actions')
            ->where('complaint_id', $id)
            ->whereNull('deleted_at')
            ->get();

        // Investigation Checks (Plan - Grouped by Category)
        $checks = DB::table('investigation_checks')
            ->where('complaint_id', $id)
             ->whereNull('deleted_at') 
            ->orderBy('display_order')
            ->get();
        $complaint->investigation_checks = $checks->groupBy('category');

        //  RCA 5 Whys (Plan)
        $complaint->rca_5_whys = DB::table('rca_five_whys')
            ->where('complaint_id', $id)
            ->whereNull('deleted_at')
            ->orderBy('iteration')
            ->get();

        // 5. RCA Fishbones (Plan - Grouped by Category)
        $fishbones = DB::table('rca_fishbones')
            ->where('complaint_id', $id)
            ->whereNull('deleted_at')
            ->get();

        $complaint->rca_fishbones = $fishbones->groupBy('category');

        // 6. Corrective Actions (Plan & Do)
        $complaint->corrective_actions = DB::table('corrective_actions')
            ->where('complaint_id', $id)
            ->whereNull('deleted_at')
            ->get();

        // 7. Verifications (Check)
        $complaint->verifications = DB::table('verifications')
            ->where('complaint_id', $id)
            ->whereNull('deleted_at')
            ->get();

        // 8. Standardizations (Act)
        $complaint->standardizations = DB::table('standardizations')
            ->where('complaint_id', $id)
            ->whereNull('deleted_at')
            ->first(); // Quan hệ 1-1

        // 9. Attachments
        $complaint->attachments = DB::table('attachments')
            ->where('record_id', $id)
            ->whereNull('deleted_at')
            ->get();

        return response()->json((array) $complaint);
    }

    protected function parseSort(string $input): array
    {
        $input = trim($input);
        $dir = 'asc';
        if (str_starts_with($input, '-')) {
            $dir = 'desc';
            $input = substr($input, 1);
        }
        return [$input, $dir];
    }
}