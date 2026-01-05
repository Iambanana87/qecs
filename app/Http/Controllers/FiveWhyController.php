<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFiveWhyRequest;
use App\Http\Requests\UpdateFiveWhyRequest; 
use App\Http\Resources\FiveWhyResource;
use App\DTOs\FiveWhyDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FiveWhyController extends Controller
{

    public function index(Request $request)
    {
        $query = DB::table('five_whys')->whereNull('deleted_at');

        if ($request->has('complaint_id')) {
            $query->where('complaint_id', $request->query('complaint_id'));
        }

        $paginator = $query->orderBy('created_at', 'desc')->paginate(10);

        $paginator->getCollection()->transform(function ($row) {
            return FiveWhyDTO::fromDb($row);
        });

        return FiveWhyResource::collection($paginator);
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new FiveWhyResource(FiveWhyDTO::fromDb($row)),
        ]);
    }

    public function store(StoreFiveWhyRequest $request): JsonResponse
    {

        $dto = $this->handleUpsert($request->validated());
        return response()->json([
            'success' => true,
            'data' => new FiveWhyResource($dto),
        ], 201);
    }

    public function update(UpdateFiveWhyRequest $request, string $id): JsonResponse
    {
        $row = $this->findRow($id);
        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data = $request->validated();
        
        // Chuẩn bị dữ liệu update
        $updateData = [
            'updated_at' => now(),
        ];

        // Map các trường dữ liệu (chỉ update cái nào có gửi lên)
        $fields = ['what', 'where', 'when', 'who', 'which', 'how', 'phenomenon_description'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        // Xử lý riêng trường photos (Json encode)
        if (array_key_exists('photos', $data)) {
            $updateData['photos'] = !empty($data['photos']) ? json_encode($data['photos']) : null;
        }

        DB::table('five_whys')->where('id', $id)->update($updateData);

        // Lấy lại dữ liệu mới nhất
        $updatedRow = $this->findRow($id);

        return response()->json([
            'success' => true,
            'data' => new FiveWhyResource(FiveWhyDTO::fromDb($updatedRow)),
        ]);
    }


    public function destroy(string $id): JsonResponse
    {
        $row = $this->findRow($id);

        if (!$row) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Thực hiện Soft Delete (Update deleted_at)
        DB::table('five_whys')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }

    // --- Helpers ---

    /**
     * Helper tìm row và check soft delete
     */
    private function findRow(string $id)
    {
        return DB::table('five_whys')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    protected function handleUpsert(array $data): FiveWhyDTO
    {

        $commonData = [
            'what' => $data['what'] ?? null,
            'where' => $data['where'] ?? null,
            'when' => $data['when'] ?? null,
            'who' => $data['who'] ?? null,
            'which' => $data['which'] ?? null,
            'how' => $data['how'] ?? null,
            'phenomenon_description' => $data['phenomenon_description'] ?? null,
            'photos' => isset($data['photos']) ? json_encode($data['photos']) : null,
            'updated_at' => now(),
        ];

        $existing = DB::table('five_whys')
            ->where('complaint_id', $data['complaint_id'])
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            DB::table('five_whys')->where('id', $existing->id)->update($commonData);
        } else {
            DB::table('five_whys')->insert(array_merge($commonData, [
                'id' => (string) Str::uuid(),
                'complaint_id' => $data['complaint_id'],
                'created_at' => now(),
            ]));
        }

        $row = DB::table('five_whys')
            ->where('complaint_id', $data['complaint_id'])
            ->whereNull('deleted_at')
            ->first();

        return FiveWhyDTO::fromDb($row);
    }
}