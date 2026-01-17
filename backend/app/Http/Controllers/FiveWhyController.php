<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFiveWhyRequest;
use App\Http\Resources\FiveWhyResource;
use App\DTOs\FiveWhyDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FiveWhyController extends Controller
{
    // --- SHOW (SỬA LẠI: Tìm theo complaint_id) ---
    public function show(string $id): JsonResponse
    {
        $complaintId = $id; 

        // 1. Tìm bản ghi Five Why dựa trên COMPLAINT_ID
        $row = DB::table('five_whys')
            ->where('complaint_id', $complaintId) 
            ->whereNull('deleted_at')
            ->first();

        if (!$row) {
             return response()->json([
                'success' => false,
                'message' => 'Five Why report not found for this Complaint'
            ], 404);
        }
        
        $attachments = DB::table('attachments')
            ->where('record_id', $row->id)
            ->where('table_name', 'five_whys')
            ->get();

        $dto = FiveWhyDTO::fromDb($row, $attachments);

        return response()->json([
            'success' => true,
            'data' => new FiveWhyResource($dto),
        ]);
    }

    // --- STORE (Giữ nguyên logic nhưng lưu ý Attachments) ---
    public function store(StoreFiveWhyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $complaintId = $data['complaint_id'];
        
        $existing = DB::table('five_whys')
            ->where('complaint_id', $complaintId)
            ->whereNull('deleted_at')
            ->first();

        $now = now();
        
        if ($existing) {
            // Logic Update (nếu tồn tại)
            $recordId = $existing->id;
            DB::table('five_whys')->where('id', $recordId)->update([
                'what' => $data['what'] ?? null,
                'where' => $data['where'] ?? null,
                'when' => $data['when'] ?? null,
                'who' => $data['who'] ?? null,
                'which' => $data['which'] ?? null,
                'how' => $data['how'] ?? null,
                'phenomenon_description' => $data['phenomenon_description'] ?? null,
                'updated_at' => $now,
            ]);
        } else {
            // Logic Create mới
            $recordId = (string) Str::uuid();
            DB::table('five_whys')->insert([
                'id' => $recordId,
                'complaint_id' => $complaintId,
                'what' => $data['what'] ?? null,
                'where' => $data['where'] ?? null,
                'when' => $data['when'] ?? null,
                'who' => $data['who'] ?? null,
                'which' => $data['which'] ?? null,
                'how' => $data['how'] ?? null,
                'phenomenon_description' => $data['phenomenon_description'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->processAttachments($request, $recordId);

        $updatedRow = DB::table('five_whys')->where('id', $recordId)->first();
        $attachments = DB::table('attachments')->where('record_id', $recordId)->get();

        return response()->json([
            'success' => true,
            'data' => new FiveWhyResource(FiveWhyDTO::fromDb($updatedRow, $attachments)),
        ], 201); 
    }
    public function update(Request $request, string $id): JsonResponse
    {
        $complaintId = $id; 

        $existing = DB::table('five_whys')
            ->where('complaint_id', $complaintId)
            ->whereNull('deleted_at')
            ->first();

        if (!$existing) {
            return response()->json(['message' => 'Five Why report not found'], 404);
        }
        $data = $request->all();
        $now = now();

        $updateData = [
            'updated_at' => $now,
        ];

        $fields = ['what', 'where', 'when', 'who', 'which', 'how', 'phenomenon_description'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        DB::table('five_whys')->where('id', $existing->id)->update($updateData);

        $this->processAttachments($request, $existing->id);

        $updatedRow = DB::table('five_whys')->where('id', $existing->id)->first();
        $attachments = DB::table('attachments')->where('record_id', $existing->id)->get();

        return response()->json([
            'success' => true,
            'data' => new FiveWhyResource(FiveWhyDTO::fromDb($updatedRow, $attachments)),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $complaintId = $id;

        $existing = DB::table('five_whys')
            ->where('complaint_id', $complaintId)
            ->whereNull('deleted_at')
            ->first();

        if (!$existing) {
            return response()->json(['message' => 'Five Why report not found'], 404);
        }

        DB::table('five_whys')
            ->where('id', $existing->id)
            ->update(['deleted_at' => now()]);

        // [Tùy chọn] Có thể bạn muốn Soft Delete luôn cả attachments liên quan?
        // Nếu muốn, bỏ comment dòng dưới:
        /*
        DB::table('attachments')
            ->where('record_id', $existing->id)
            ->update(['deleted_at' => now()]); // Cần thêm cột deleted_at vào bảng attachments trước
        */

        return response()->json([
            'success' => true,
            'message' => 'Five Why report deleted successfully',
        ]);
    }

    // --- Helper Upload ---
    private function processAttachments(Request $request, string $recordId)
    {
        $sections = ['what', 'where', 'when', 'who', 'which', 'how'];
        $insertFiles = [];
        $now = now();
        
        // Tạo tên thư mục theo ngày: VD "2024-05-21"
        $dateFolder = date('Y-m-d'); 

        foreach ($sections as $section) {
            $inputKey = $section . '_files'; 

            if ($request->hasFile($inputKey)) {
                foreach ($request->file($inputKey) as $file) {
                    
                    // CẤU TRÚC: section / ngày / tên_file_hash
                    // Ví dụ: five_whys/2024-05-21/randomname.jpg
                    $folderPath = 'five_whys/' . $dateFolder;

                    // Lưu file dùng disk 'external_uploads'
                    $path = $file->store($folderPath, 'external_uploads');

                    $insertFiles[] = [
                        'id' => (string) \Illuminate\Support\Str::uuid(),
                        'record_id' => $recordId,
                        'table_name' => 'five_whys',
                        'section' => $section,
                        'file_name' => $file->getClientOriginalName(),
                        
                        'file_path' => $path, // Lưu đường dẫn tương đối (five_whys/2024-05-21/xxx.jpg)
                        
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($insertFiles)) {
            \Illuminate\Support\Facades\DB::table('attachments')->insert($insertFiles);
        }
    }
}