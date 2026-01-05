<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Tạo Users (10 người)
        // Lưu ý: Migration bạn khai báo là 'User' (viết hoa), nên ở đây dùng bảng 'User'
        $userIds = [];
        for ($i = 0; $i < 10; $i++) {
            $id = Str::uuid()->toString();
            $userIds[] = $id;
            DB::table('User')->insert([
                'id' => $id,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'role' => $faker->randomElement(['Admin', 'Manager', 'Staff', 'Operator']),
                'avatar_url' => $faker->imageUrl(100, 100, 'people'),
                'status' => $faker->randomElement(['online', 'idle', 'offline', 'busy']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Tạo Partners (10 đối tác)
        $partnerIds = [];
        for ($i = 0; $i < 10; $i++) {
            $id = Str::uuid()->toString();
            $partnerIds[] = $id;
            DB::table('partners')->insert([
                'id' => $id,
                'name' => $faker->company,
                'code' => strtoupper($faker->bothify('PART-####')),
                'country' => $faker->country,
                'contact' => $faker->name . ' (' . $faker->phoneNumber . ')',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. TẠO 50 COMPLAINTS VÀ DỮ LIỆU LIÊN QUAN (CORE LOGIC)
        for ($i = 0; $i < 50; $i++) {
            $complaintId = Str::uuid()->toString();
            $creatorId = $faker->randomElement($userIds);
            $partnerId = $faker->randomElement($partnerIds);
            
            // Random thời gian để tạo tính chân thực
            $createdAt = $faker->dateTimeBetween('-1 year', 'now');
            $dateOccurrence = $faker->dateTimeBetween('-2 years', $createdAt);
            
            // Status flow
            $status = $faker->randomElement(['Draft', 'Submitted', 'PLAN', 'DO', 'CHECK', 'ACT', 'Closed', 'CANCELLED']);
            
            // 3.1 Insert Complaint (Header)
            DB::table('complaints')->insert([
                'id' => $complaintId,
                'report_number' => 'RPT-' . $createdAt->format('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'created_by' => $creatorId,
                'subject' => $faker->sentence(6),
                'status' => $status,
                
                // General Info
                'department' => $faker->jobTitle,
                'manager' => $faker->name,
                'line_area' => 'Line ' . $faker->randomDigitNotNull,
                'incident_type' => $faker->randomElement(['Quality', 'Safety', 'Process']),
                'product_description' => $faker->text(100),
                
                'lot_code' => strtoupper($faker->bothify('LOT-??##')), // Updated column name
                'product_code' => strtoupper($faker->bothify('PRD-####')),
                'machine' => 'MC-' . $faker->randomDigitNotNull,
                'date_code' => $createdAt->format('dmY'),
                
                'date_occurrence' => $dateOccurrence,
                'date_detection' => $faker->dateTimeBetween($dateOccurrence, $createdAt),
                'date_report' => $createdAt,
                
                'unit_qty_audited' => $faker->randomFloat(2, 100, 1000), // Decimal
                'unit_qty_rejected' => $faker->randomFloat(2, 1, 50),   // Decimal
                
                'severity_level' => $faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
                'category' => $faker->word,
                
                'report_completed_by' => $faker->name,
                'detection_point' => $faker->randomElement(['Incoming', 'In-Process', 'Outgoing', 'Customer']),
                'partner_id' => $partnerId,
                
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.2 Insert Problem Description (1-1) - 5W1H
            DB::table('five_whys')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'what' => $faker->sentence,
                'where' => $faker->city,
                'when' => $dateOccurrence->format('Y-m-d H:i:s'),
                'who' => $faker->name,
                'which' => 'Lot ' . $faker->bothify('##??'),
                'how' => $faker->sentence,
                'phenomenon_description' => $faker->paragraph,
                'photos' => $faker->imageUrl(), // Lưu URL giả
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.3 Insert 5M Analysis (Type-based rows)
            // Vì cấu trúc mới là dạng dọc (type column), ta loop qua 5 loại
            $fiveMTypes = ['Man', 'Machine', 'Method', 'Material', 'Environment'];
            foreach ($fiveMTypes as $type) {
                DB::table('five_m_analyses')->insert([
                    'id' => Str::uuid()->toString(),
                    'complaint_id' => $complaintId,
                    'type' => $type,
                    'code' => strtoupper(substr($type, 0, 3)) . '-' . $faker->randomDigit,
                    'cause' => $faker->sentence,
                    'confirmed' => $faker->boolean,
                    'description' => $faker->text(50),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            // 3.4 Insert Why Why Analysis
            // Tạo 1 bản ghi cho HAPPEN và 1 bản ghi cho DETECTION
            foreach (['HAPPEN', 'DETECTION'] as $type) {
                DB::table('why_why_analyses')->insert([
                    'id' => Str::uuid()->toString(),
                    'complaint_id' => $complaintId,
                    'analysis_type' => $type,
                    'why1' => $faker->sentence,
                    'why2' => $faker->sentence,
                    'why3' => $faker->sentence,
                    'why4' => $faker->sentence,
                    'why5' => $faker->sentence,
                    'root_cause' => $faker->sentence,
                    'capa_ref' => 'CAPA-' . $faker->randomDigit,
                    'status' => 'Done',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            // 3.5 Insert Check Material Machines
            DB::table('check_material_machines')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'machine' => 'Machine-' . $faker->randomDigit,
                'sub_assembly' => 'Gearbox',
                'component' => 'Bearing',
                'description' => 'Noise issue',
                'current_condition' => 'Worn out',
                'before_photo' => $faker->imageUrl(),
                'after_photo' => $faker->imageUrl(),
                'respons' => $faker->name,
                'control_frequency' => 'Daily',
                'status' => 'NG',
                'close_date' => $faker->date(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.6 Insert Check Parameters Operations
            DB::table('check_parameters_operations')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'machine' => 'CNC-01',
                'sub_assembly' => 'Spindle',
                'component' => 'Speed',
                'description' => 'Speed fluctuation',
                'current_condition' => 'Unstable',
                'respons' => $faker->name,
                'status' => 'NG',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.7 Insert Immediate Actions
            DB::table('immediate_actions')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'action' => 'Stop production line',
                'status' => 'Done',
                'responsible' => $faker->name,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.8 Insert Corrective Actions
            DB::table('corrective_actions')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'action' => 'Replace sensor',
                'responsible' => $faker->name,
                'end_date' => $faker->date(),
                'verification' => $faker->boolean,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.9 Insert Effectiveness Checks
            DB::table('effectiveness_checks')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'produce_cause' => $faker->boolean,
                'no' => 1,
                'action' => 'Monitor for 24 hours',
                'responsible' => $faker->name,
                'end_date' => $faker->date(),
                'verification' => true,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.10 Insert Preventive Actions
            DB::table('preventive_actions')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'action' => 'Update SOP version 2.0',
                'responsible' => $faker->name,
                'end_date' => $faker->date(),
                'verification' => false,
                // Các trường đại diện
                'complaint_responsible' => $faker->name,
                'production_representative' => $faker->name,
                'quality_representative' => $faker->name,
                'engineering_representative' => $faker->name,
                'quality_manager' => $faker->name,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // 3.11 Attachments
            if ($faker->boolean(70)) { 
                DB::table('attachments')->insert([
                    'id' => Str::uuid()->toString(),
                    'record_id' => $complaintId,
                    'record_type' => 'App\Models\Complaint', // Hoặc 'complaints' tùy convention
                    'context' => 'EVIDENCE',
                    'file_name' => 'evidence_' . $i . '.jpg',
                    'file_url' => $faker->imageUrl(),
                    'file_type' => 'image/jpeg',
                    'file_size' => $faker->numberBetween(1000, 50000),
                    'uploaded_at' => $createdAt,
                ]);
            }
        }

        // 4. Các bảng phụ (Giữ nguyên hoặc cập nhật nếu cần)
        
        // Country
        $countries = ['Vietnam', 'USA', 'Japan', 'Korea', 'Germany'];
        foreach ($countries as $c) {
            DB::table('Country')->insert(['id' => Str::uuid()->toString(), 'name' => $c]);
        }

        // Quality Alert
        for ($i = 0; $i < 5; $i++) {
            DB::table('QualityAlert')->insert([
                'id' => Str::uuid()->toString(),
                'reference' => 'QA-' . $faker->year . '-' . $i,
                'title' => $faker->sentence,
                'type' => 'Quality',
                'severity' => 'High',
                'status' => 'Active',
                'description' => $faker->paragraph,
                'issued_by' => $faker->randomElement($userIds),
                'created_date' => now(),
            ]);
        }

        // Notification
        for ($i = 0; $i < 20; $i++) {
            DB::table('Notification')->insert([
                'id' => Str::uuid()->toString(),
                'type' => 'System',
                'title' => 'New Complaint Created',
                'description' => 'Complaint needs review.',
                'timestamp' => now(),
                'isUnread' => $faker->boolean,
            ]);
        }
    }
}