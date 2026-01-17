<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFiveMAnalysisRequest;
use App\Models\FiveMAnalysis;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\DB;

use App\DTOs\FiveMAnalysisDTO;
use App\Http\Resources\FiveMAnalysisResource;

class FiveMAnalysisController extends Controller
{
    public function store(StoreFiveMAnalysisRequest $request)
    {
        $dto = $this->handleUpsert($request->validated());

        return response()->json([
            'success' => true,
            'data' => new FiveMAnalysisResource($dto),
        ]);
    }

    protected function handleUpsert(array $data): FiveMAnalysisDTO
    {
        DB::table('five_m_analyses')->updateOrInsert(
            [
                'complaint_id' => $data['complaint_id'],
                'type' => $data['type'],
                'deleted_at' => null,
            ],
            [
                'code' => $data['code'] ?? null,
                'cause' => $data['cause'] ?? null,
                'description' => $data['description'] ?? null,
                'confirmed' => $data['confirmed'] ?? false,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $row = DB::table('five_m_analyses')
            ->where('complaint_id', $data['complaint_id'])
            ->where('type', $data['type'])
            ->whereNull('deleted_at')
            ->first();

        return FiveMAnalysisDTO::fromDb($row);
    }
}

