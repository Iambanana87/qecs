<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =============================================
        // 1. MASTER DATA (User, Country)
        // =============================================
        
        // Tạo Country
        $countryIds = [];
        $countries = ['Vietnam', 'USA', 'Japan', 'Korea', 'Germany', 'Singapore'];
        foreach ($countries as $cName) {
            $id = Str::uuid()->toString();
            $countryIds[] = $id;
            DB::table('Country')->insert([ // Lưu ý: Tên bảng trong migration của bạn là 'Country' (viết hoa)
                'id' => $id,
                'name' => $cName
            ]);
        }

        // Tạo Users
        $userIds = [];
        $roles = ['Admin', 'QA Manager', 'QA Staff', 'Production Leader'];
        
        // Tạo 10 users mẫu
        for ($i = 0; $i < 10; $i++) {
            $id = Str::uuid()->toString();
            $userIds[] = $id;
            DB::table('User')->insert([ // Tên bảng 'User'
                'id' => $id,
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'role' => fake()->randomElement($roles),
                'avatar_Url' => fake()->imageUrl(100, 100, 'people'), // Theo migration của bạn là avatar_Url
                'status' => fake()->randomElement(['online', 'idle', 'offline', 'busy']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =============================================
        // 2. TRANSACTION DATA (Complaint & PDCA Children)
        // =============================================

        $complaintStatuses = ['PLAN', 'DO', 'CHECK', 'ACT', 'CLOSED', 'CANCELLED'];

        // Tạo 50 Complaints
        for ($i = 0; $i < 50; $i++) {
            $complaintId = Str::uuid()->toString();
            $createdDate = fake()->dateTimeBetween('-1 year', 'now');
            $status = fake()->randomElement($complaintStatuses);
            
            // 2.1 Insert Complaint
            DB::table('complaints')->insert([
                'id' => $complaintId,
                'report_number' => 'COM-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'product_name' => 'Product ' . fake()->randomElement(['A', 'B', 'C', 'X', 'Y']),
                'lot_number' => 'LOT-' . fake()->bothify('####??'),
                'mfg_date' => Carbon::instance($createdDate)->subMonths(1),
                'exp_date' => Carbon::instance($createdDate)->addYears(1),
                'total_quantity' => fake()->numberBetween(1000, 5000),
                'defect_quantity' => fake()->numberBetween(1, 100),
                'description' => fake()->paragraph(),
                'defect_location' => fake()->randomElement(['Surface', 'Internal', 'Packaging']),
                'customer_id' => Str::uuid()->toString(), // Giả lập ID Partner
                'created_by' => fake()->randomElement($userIds),
                'status' => $status,
                'created_at' => $createdDate,
                'updated_at' => now(),
            ]);

            // 2.2 Insert Containment Actions (Hành động ngăn chặn)
            // Mỗi complaint có 1-2 hành động ngăn chặn
            for ($j = 0; $j < rand(1, 2); $j++) {
                DB::table('containment_actions')->insert([
                    'id' => Str::uuid()->toString(),
                    'complaint_id' => $complaintId,
                    'action_content' => fake()->sentence(),
                    'person_in_charge' => fake()->randomElement($userIds),
                    'due_date' => Carbon::instance($createdDate)->addDays(2),
                    'status' => fake()->randomElement(['Pending', 'In_Progress', 'Completed']),
                    'completion_date' => fake()->boolean() ? Carbon::instance($createdDate)->addDays(1) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2.3 Insert Investigation Checks (Checklist)
            $categories = ['Material_Machine', 'Parameter_Operation'];
            foreach ($categories as $cat) {
                DB::table('investigation_checks')->insert([
                    'id' => Str::uuid()->toString(),
                    'complaint_id' => $complaintId,
                    'category' => $cat,
                    'check_item' => $cat == 'Material_Machine' ? 'Check Machine Temp' : 'Check Worker Skill',
                    'standard_spec' => 'Standard ' . fake()->randomNumber(2),
                    'actual_result' => 'Actual ' . fake()->randomNumber(2),
                    'status' => fake()->randomElement(['OK', 'NG']),
                    'remarks' => fake()->sentence(),
                    'display_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2.4 Insert RCA 5 Whys
            for ($k = 1; $k <= 5; $k++) {
                DB::table('rca_five_whys')->insert([ // Theo migration của bạn
                    'id' => Str::uuid()->toString(),
                    'complaint_id' => $complaintId,
                    'iteration' => $k,
                    'question' => "Why level $k?",
                    'answer' => fake()->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2.5 Insert RCA Fishbones (Xương cá)
            $fishCats = ['Man', 'Machine', 'Method', 'Material', 'Environment', 'Measurement'];
            foreach ($fishCats as $fCat) {
                if (fake()->boolean(30)) { // 30% cơ hội có nguyên nhân ở nhánh này
                    DB::table('rca_fishbones')->insert([
                        'id' => Str::uuid()->toString(),
                        'complaint_id' => $complaintId,
                        'category' => $fCat,
                        'cause_detail' => fake()->sentence(),
                        'is_root_cause' => fake()->boolean(10),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 2.6 Insert Corrective Actions (Hành động khắc phục)
            $actId = Str::uuid()->toString();
            DB::table('corrective_actions')->insert([
                'id' => $actId,
                'complaint_id' => $complaintId,
                'action_plan' => fake()->paragraph(),
                'planned_pic' => fake()->randomElement($userIds),
                'planned_due_date' => Carbon::instance($createdDate)->addDays(7),
                'implementation_details' => $status != 'PLAN' ? fake()->paragraph() : null,
                'implementation_date' => $status != 'PLAN' ? Carbon::instance($createdDate)->addDays(5) : null,
                'status' => $status == 'PLAN' ? 'Open' : fake()->randomElement(['Done', 'In_Progress']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2.7 Insert Verifications (Chỉ tạo nếu status >= CHECK)
            if (in_array($status, ['CHECK', 'ACT', 'CLOSED'])) {
                $verId = Str::uuid()->toString();
                DB::table('verifications')->insert([
                    'id' => $verId,
                    'complaint_id' => $complaintId,
                    'verified_by' => fake()->randomElement($userIds),
                    'check_date' => Carbon::instance($createdDate)->addDays(10),
                    'method' => 'Visual Inspection',
                    'result' => fake()->randomElement(['OK', 'NG']),
                    'comments' => fake()->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Tạo Attachment cho Verification
                DB::table('attachments')->insert([
                    'id' => Str::uuid()->toString(),
                    'record_id' => $verId, // Link tới bảng verification
                    'record_type' => 'App\Models\Verification', // Giả định namespace Model
                    'context' => 'VERIFICATION_EVIDENCE',
                    'file_name' => 'check_result.jpg',
                    'file_url' => 'http://example.com/check.jpg',
                    'file_type' => 'image/jpeg',
                    'file_size' => 2048,
                    'uploaded_at' => now(),
                ]);
            }

            // 2.8 Insert Standardizations (Chỉ tạo nếu status >= ACT)
            if (in_array($status, ['ACT', 'CLOSED'])) {
                DB::table('standardizations')->insert([
                    'id' => Str::uuid()->toString(),
                    'complaint_id' => $complaintId,
                    'is_sop_updated' => true,
                    'is_fmea_updated' => false,
                    'is_control_plan_updated' => true,
                    'updated_docs_detail' => 'SOP-001 Rev 2',
                    'lessons_learned' => fake()->paragraph(),
                    'closed_by' => fake()->randomElement($userIds),
                    'closed_date' => Carbon::instance($createdDate)->addDays(15),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2.9 Attachments cho Complaint Header
            DB::table('attachments')->insert([
                'id' => Str::uuid()->toString(),
                'record_id' => $complaintId, // Link tới bảng complaint
                'record_type' => 'App\Models\Complaint',
                'context' => 'COMPLAINT_HEADER',
                'file_name' => 'defect_photo.jpg',
                'file_url' => 'http://example.com/defect.jpg',
                'file_type' => 'image/jpeg',
                'file_size' => 1024,
                'uploaded_at' => now(),
            ]);
        }
        
        // =============================================
        // 3. OTHER TABLES (Audit, Alert - Optional)
        // =============================================
        // Tạo vài record cho QualityAlert để không bị trống
        for ($i = 0; $i < 5; $i++) {
            DB::table('QualityAlert')->insert([
                'id' => Str::uuid()->toString(),
                'reference' => 'QA-' . fake()->randomNumber(5),
                'title' => fake()->sentence(),
                'type' => fake()->randomElement(['Quality', 'Safety']),
                'severity' => fake()->randomElement(['High', 'Medium']),
                'status' => 'Active',
                'created_date' => now(),
            ]);
        }
    }
}