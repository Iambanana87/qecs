<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWhyWhyAnalysisRequest;
use App\Http\Resources\WhyWhyAnalysisResource;
use App\DTOs\WhyWhyAnalysisDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WhyWhyAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('why_why_analyses')->whereNull('deleted_at');

        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }
        
        // Filter theo type nếu cần
        if ($request->has('analysis_type')) {
            $query->where('analysis_type', $request->query('analysis_type'));
        }

        $rows = $query->orderBy('created_at', 'desc')->get();

        // Transform Collection -> DTO -> Resource Collection
        $dtos = $rows->map(function ($row) {
            return WhyWhyAnalysisDTO::fromDb($row);
        });

        return WhyWhyAnalysisResource::collection($dtos);
    }


    public function store(StoreWhyWhyAnalysisRequest $request): JsonResponse
    {
        $dto = $this->handleUpsert($request->validated());

        return response()->json([
            'success' => true,
            'data' => new WhyWhyAnalysisResource($dto),
        ]);
    }

    // --- Xóa mềm ---
    public function destroy(string $id): JsonResponse
    {
        $exists = DB::table('why_why_analyses')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Not found'], 404);
        }

        DB::table('why_why_analyses')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }

    // handle upsert logic
    protected function handleUpsert(array $data): WhyWhyAnalysisDTO
    {
       
        $existing = DB::table('why_why_analyses')
            ->where('complaint_id', $data['complaint_id'])
            ->where('analysis_type', $data['analysis_type'])
            ->whereNull('deleted_at')
            ->first();

        $commonData = [
            'why1' => $data['why1'] ?? null,
            'why2' => $data['why2'] ?? null,
            'why3' => $data['why3'] ?? null,
            'why4' => $data['why4'] ?? null,
            'why5' => $data['why5'] ?? null,
            'root_cause' => $data['root_cause'] ?? null,
            'capa_ref' => $data['capa_ref'] ?? null,
            'status' => $data['status'] ?? null,
            'updated_at' => now(),
        ];

        if ($existing) {
            // --- UPDATE ---
            DB::table('why_why_analyses')
                ->where('id', $existing->id)
                ->update($commonData);
        } else {
            // --- INSERT ---
            DB::table('why_why_analyses')->insert(array_merge($commonData, [
                'id' => (string) Str::uuid(),
                'complaint_id' => $data['complaint_id'],
                'analysis_type' => $data['analysis_type'],
                'created_at' => now(),
            ]));
        }

        $row = DB::table('why_why_analyses')
            ->where('complaint_id', $data['complaint_id'])
            ->where('analysis_type', $data['analysis_type'])
            ->whereNull('deleted_at')
            ->first();

        return WhyWhyAnalysisDTO::fromDb($row);
    }
}
