<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $limit = 50; // Số lượng record tối thiểu

        $this->command->info('1. Seeding Independent Tables (Parents)...');

        // 1. Seed Users
        $userIds = [];
        for ($i = 0; $i < $limit; $i++) {
            $id = Str::uuid()->toString();
            $userIds[] = $id;
            DB::table('User')->insert([
                'id' => $id,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'role' => $faker->jobTitle,
                'avatar_url' => $faker->imageUrl(),
                'status' => $faker->randomElement(['online', 'idle', 'offline', 'busy']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Seed Countries
        $countryIds = [];
        for ($i = 0; $i < $limit; $i++) {
            $id = Str::uuid()->toString();
            $countryIds[] = $id;
            DB::table('Country')->insert([
                'id' => $id,
                'name' => $faker->country,
            ]);
        }

        // 3. Seed Partners
        $partnerIds = [];
        for ($i = 0; $i < $limit; $i++) {
            $id = Str::uuid()->toString();
            $partnerIds[] = $id;
            DB::table('partners')->insert([
                'id' => $id,
                'name' => $faker->company,
                'code' => strtoupper($faker->bothify('PART-####')),
                'country' => $faker->country,
                'contact' => $faker->phoneNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Seed Customers
        $customerIds = [];
        for ($i = 0; $i < $limit; $i++) {
            $id = Str::uuid()->toString();
            $customerIds[] = $id;
            DB::table('customers')->insert([
                'id' => $id,
                'name' => $faker->company,
                'description' => $faker->catchPhrase,
                'department' => $faker->jobTitle,
                'department_manager' => $faker->name,
                'line_area' => 'Line ' . $faker->randomDigit,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('2. Seeding Main Transaction Tables...');

        // 5. Seed Complaints (Hub Table)
        $complaintIds = [];
        for ($i = 0; $i < $limit; $i++) {
            $id = Str::uuid()->toString();
            $complaintIds[] = $id;
            DB::table('complaints')->insert([
                'id' => $id,
                // CHANGE: Type is now strictly 'client' or 'supplier'
                'type' => $faker->randomElement(['client', 'supplier']), 
                'complaint_no' => $faker->unique()->bothify('NO-####'), // Moved unique constraint here if needed
                'subject' => $faker->sentence,
                'customer_id' => $faker->randomElement($customerIds),
                'incident_type' => $faker->randomElement(['Safety', 'Quality', 'Environment']),
                'category' => $faker->word,
                'severity_level' => $faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
                'machine' => 'Machine-' . $faker->randomDigit,
                'report_completed_by' => $faker->name,
                'lot_code' => $faker->bothify('LOT-###'),
                'product_code' => $faker->bothify('PROD-###'),
                'unit_qty_audited' => $faker->numberBetween(100, 1000),
                'unit_qty_rejected' => $faker->numberBetween(1, 50),
                'date_code' => now()->format('Ymd'),
                'date_occurrence' => $faker->dateTimeThisYear(),
                'date_detection' => $faker->dateTimeThisYear(),
                'date_report' => now(),
                'product_description' => $faker->text(50),
                'detection_point' => 'QA Gate',
                'photo' => $faker->imageUrl(),
                'detection_method' => 'Visual Inspection',
                'partner_id' => $faker->randomElement($partnerIds),
                'attachment' => null,
                'floor_process_visualization' => json_encode(['step1' => 'ok']),
                'five_why_id' => null, 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Seed Audits
        $auditIds = [];
        for ($i = 0; $i < $limit; $i++) {
            $id = Str::uuid()->toString();
            $auditIds[] = $id;
            DB::table('Audit')->insert([
                'id' => $id,
                'reference' => $faker->bothify('AUD-####'),
                'subject' => $faker->sentence,
                'type' => $faker->randomElement(['Internal_Audit', 'External_Audit']),
                'standard_id' => Str::uuid()->toString(), 
                'company' => $faker->company,
                'stage' => $faker->randomElement(['Planned', 'In_Progress', 'Reporting', 'Closed']),
                'external_ref_no' => $faker->bothify('EXT-####'),
                'start_date' => $faker->dateTimeThisMonth(),
                'end_date' => $faker->dateTimeThisMonth(),
            ]);
        }

        // 7. Seed Quality Alerts
        $alertIds = [];
        for ($i = 0; $i < $limit; $i++) {
            $id = Str::uuid()->toString();
            $alertIds[] = $id;
            DB::table('QualityAlert')->insert([
                'id' => $id,
                'reference' => $faker->bothify('ALERT-####'),
                'title' => $faker->sentence,
                'type' => $faker->randomElement(['Quality', 'Safety', 'Labeling']),
                'severity' => $faker->randomElement(['Low', 'Medium', 'High']),
                'status' => $faker->randomElement(['Draft', 'Active', 'Closed']),
                'description' => $faker->paragraph,
                'immediate_instruction' => $faker->sentence,
                'issued_by' => $faker->randomElement($userIds),
                'acknowledgement_required' => $faker->boolean,
                'created_date' => now(),
                'effective_date' => now(),
                'expiration_date' => $faker->dateTimeBetween('now', '+1 year'),
                'related_complaint_id' => $faker->randomElement($complaintIds),
                'related_action_id' => Str::uuid()->toString(),
            ]);
        }

        $this->command->info('3. Seeding Dependent Tables (Relations)...');

        // 8. Seed Five Whys (1-to-1 with Complaints)
        foreach ($complaintIds as $complaintId) {
            $fiveWhyId = Str::uuid()->toString();
            DB::table('five_whys')->insert([
                'id' => $fiveWhyId,
                'complaint_id' => $complaintId,
                'what' => $faker->sentence,
                'where' => $faker->city,
                'when' => $faker->date,
                'who' => $faker->name,
                'which' => $faker->word,
                'how' => $faker->sentence,
                'phenomenon_description' => $faker->paragraph,
                'photos' => $faker->imageUrl(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('complaints')->where('id', $complaintId)->update(['five_why_id' => $fiveWhyId]);
        }

        // 9. Seed Attachments
        for ($i = 0; $i < $limit; $i++) {
            DB::table('attachments')->insert([
                'id' => Str::uuid()->toString(),
                'record_id' => $faker->randomElement($complaintIds),
                'record_type' => 'App\Models\Complaint',
                'context' => 'evidence',
                'file_name' => $faker->word . '.pdf',
                'file_url' => $faker->url,
                'file_type' => 'application/pdf',
                'file_size' => $faker->numberBetween(1000, 50000),
                'uploaded_at' => now(),
            ]);
        }

        // 10. Seed Corrective Actions
        $actionIds = [];
        foreach ($complaintIds as $complaintId) {
             for($k=0; $k < rand(1,2); $k++) {
                $actId = Str::uuid()->toString();
                $actionIds[] = $actId;
                DB::table('corrective_actions')->insert([
                    'id' => $actId,
                    'complaint_id' => $complaintId,
                    'no' => $k + 1,
                    'action' => $faker->sentence,
                    'responsible' => $faker->name,
                    'end_date' => $faker->dateTimeBetween('now', '+1 month'),
                    'verification' => $faker->boolean,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
             }
        }

        // 11. Seed Postpone Records
        foreach(array_slice($actionIds, 0, 20) as $actId) {
            DB::table('PostponeRecord')->insert([
                'id' => Str::uuid()->toString(),
                'action_id' => $actId,
                'requested_by' => $faker->randomElement($userIds),
                'requested_date' => now(),
                'old_due_date' => now(),
                'new_due_date' => $faker->dateTimeBetween('+1 week', '+1 month'),
                'reason' => $faker->sentence,
                'status' => $faker->randomElement(['Waiting_for_Approval', 'Approved']),
            ]);
        }

        // 12. Seed Other Analysis Tables
        foreach ($complaintIds as $complaintId) {
            // Check Material Machines
            DB::table('check_material_machines')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'machine' => 'Machine A',
                'description' => $faker->sentence,
                'current_condition' => 'Bad',
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Check Parameters Operations
            DB::table('check_parameters_operations')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'machine' => 'Machine A',
                'description' => $faker->sentence,
                'current_condition' => 'Bad',
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

             // Immediate Actions
             DB::table('immediate_actions')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'action' => 'Stop Line',
                'status' => 'Done',
                'responsible' => $faker->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Five M Analysis
            foreach(['MAN', 'MACHINE', 'METHOD', 'MATERIAL', 'ENVIRONMENT'] as $type) {
                DB::table('five_m_analyses')->insert([
                    'id' => Str::uuid()->toString(),
                    'complaint_id' => $complaintId,
                    'type' => $type,
                    'code' => $faker->word,
                    'cause' => $faker->sentence,
                    'confirmed' => $faker->boolean,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Why Why Analysis
            DB::table('why_why_analyses')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'analysis_type' => 'HAPPEN',
                'why1' => $faker->sentence,
                'why2' => $faker->sentence,
                'why3' => $faker->sentence,
                'root_cause' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Preventive Actions
            DB::table('preventive_actions')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'no' => 1,
                'action' => $faker->sentence,
                'responsible' => $faker->name,
                'end_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Effectiveness Checks
            DB::table('effectiveness_checks')->insert([
                'id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'produce_cause' => true,
                'no' => 1,
                'action' => 'Check again',
                'responsible' => $faker->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 13. Audit Trail
        for ($i = 0; $i < $limit; $i++) {
            DB::table('AuditTrail')->insert([
                'id' => Str::uuid()->toString(),
                'table_name' => 'complaints',
                'record_id' => $faker->randomElement($complaintIds),
                'user_id' => $faker->randomElement($userIds),
                'action' => 'UPDATE',
                'field_name' => 'status',
                'old_value' => 'Open',
                'new_value' => 'Closed',
                'timestamp' => now(),
            ]);
        }
        
        // 14. Notifications
        for ($i = 0; $i < $limit; $i++) {
             DB::table('Notification')->insert([
                'id' => Str::uuid()->toString(),
                'type' => 'Alert',
                'title' => 'New Complaint Assigned',
                'description' => $faker->sentence,
                'timestamp' => now(),
                'isUnread' => true,
                'link' => '/complaints/' . $faker->randomElement($complaintIds),
            ]);
        }

        $this->command->info('Seeding completed successfully!');
    }
}