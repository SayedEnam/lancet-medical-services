<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\MedicalTest;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Report;
use App\Models\Supplier;
use App\Models\TestCategory;
use App\Models\TestOrder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class DiagnosticDemoSeeder extends Seeder
{
    public function run(): void
    {
        $receptionUser = User::firstOrCreate(['email' => 'reception@lancet.com'], ['name' => 'Reception Desk', 'password' => 'password', 'status' => 'active']);
        $receptionUser->assignRole('Receptionist');

        $this->seedTests();
        $this->seedAccounting();
        $this->seedInventory();
        $this->seedStaff();

        $doctorSeeds = [
            ['email' => 'doctor1@lancet.com', 'name' => 'Dr. Rahman', 'specialization' => 'Internal Medicine'],
            ['email' => 'doctor2@lancet.com', 'name' => 'Dr. Ahmed', 'specialization' => 'Cardiology'],
            ['email' => 'doctor3@lancet.com', 'name' => 'Dr. Sultana', 'specialization' => 'Pathology'],
            ['email' => 'doctor4@lancet.com', 'name' => 'Dr. Karim', 'specialization' => 'Radiology'],
        ];

        $doctors = collect($doctorSeeds)->map(function ($seed) {
            $user = User::firstOrCreate(['email' => $seed['email']], ['name' => $seed['name'], 'password' => 'password', 'status' => 'active']);
            $user->assignRole('Doctor');

            return Doctor::firstOrCreate(
                ['name' => $seed['name'], 'specialization' => $seed['specialization']],
                ['user_id' => $user->id, 'consultation_fee' => rand(700, 1500), 'commission_percent' => rand(8, 15), 'is_available' => true]
            );
        });

        for ($i = 1; $i <= 30; $i++) {
            $patient = Patient::firstOrCreate(
                ['patient_id' => 'P-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => 'Demo Patient ' . $i,
                    'email' => 'patient' . $i . '@lancet.test',
                    'phone' => '01710000' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'gender' => $i % 2 === 0 ? 'female' : 'male',
                    'blood_group' => ['A+', 'B+', 'O+', 'AB+'][$i % 4],
                    'emergency_contact' => '01710000' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                ]
            );
            $patient->update([
                'name' => $patient->name ?: 'Demo Patient ' . $i,
                'email' => $patient->email ?: 'patient' . $i . '@lancet.test',
                'phone' => $patient->phone ?: '01710000' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            ]);

            if (Appointment::where('patient_id', $patient->id)->exists()) {
                continue;
            }

            $doctor = $doctors->random();
            $appt = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => Carbon::today()->subDays(rand(0, 8))->toDateString(),
                'appointment_time' => sprintf('%02d:%02d:00', rand(9, 17), [0, 15, 30, 45][array_rand([0, 1, 2, 3])]),
                'status' => ['scheduled', 'checked_in', 'completed'][array_rand(['scheduled', 'checked_in', 'completed'])],
                'is_walk_in' => (bool) rand(0, 1),
                'queue_no' => $i,
            ]);

            $testOrder = TestOrder::create([
                'patient_id' => $patient->id,
                'appointment_id' => $appt->id,
                'barcode' => 'BC' . now()->format('His') . $i,
                'status' => ['sample_collected', 'processing', 'completed', 'verified'][array_rand(['sample_collected', 'processing', 'completed', 'verified'])],
            ]);

            $invoiceTotal = rand(1200, 4500);
            $paid = rand(500, $invoiceTotal);
            $invoice = Invoice::updateOrCreate([
                'invoice_no' => 'INV-' . now()->format('ymd') . '-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
            ], [
                'patient_id' => $patient->id,
                'sub_total' => $invoiceTotal,
                'discount' => rand(0, 200),
                'vat' => rand(50, 250),
                'total_amount' => $invoiceTotal,
                'paid_amount' => $paid,
                'due_amount' => max(0, $invoiceTotal - $paid),
                'status' => $paid >= $invoiceTotal ? 'paid' : 'partial',
            ]);

            Payment::firstOrCreate([
                'invoice_id' => $invoice->id,
                'transaction_no' => 'DEMO-PAY-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
            ], [
                'amount' => $paid,
                'method' => ['cash', 'card', 'bkash', 'nagad'][$i % 4],
                'paid_at' => now()->subDays(rand(0, 8)),
            ]);

            Report::firstOrCreate([
                'test_order_id' => $testOrder->id,
            ], [
                'test_order_id' => $testOrder->id,
                'type' => ['hematology', 'biochemistry', 'serology'][array_rand(['hematology', 'biochemistry', 'serology'])],
                'status' => ['processing', 'pending_approval', 'approved', 'delivered'][array_rand(['processing', 'pending_approval', 'approved', 'delivered'])],
                'qr_code' => Str::uuid()->toString(),
            ]);

            Activity::create([
                'log_name' => 'demo',
                'description' => "Demo appointment #{$appt->id} created",
                'subject_type' => Appointment::class,
                'subject_id' => $appt->id,
                'causer_type' => User::class,
                'causer_id' => $receptionUser->id,
                'event' => 'created',
                'properties' => [],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Report::whereNull('qr_code')->get()->each(function (Report $report) {
            $report->update(['qr_code' => Str::uuid()->toString()]);
        });
    }

    private function seedTests(): void
    {
        $categories = [
            ['name' => 'Blood Test', 'description' => 'Routine hematology and blood screening'],
            ['name' => 'Urine Test', 'description' => 'Urinalysis and kidney health markers'],
            ['name' => 'Hormone Test', 'description' => 'Thyroid, fertility, and endocrine tests'],
            ['name' => 'Imaging', 'description' => 'X-Ray, ultrasound, ECG, and radiology services'],
        ];

        foreach ($categories as $category) {
            TestCategory::firstOrCreate(['name' => $category['name']], $category);
        }

        $blood = TestCategory::where('name', 'Blood Test')->first();
        $urine = TestCategory::where('name', 'Urine Test')->first();
        $hormone = TestCategory::where('name', 'Hormone Test')->first();
        $imaging = TestCategory::where('name', 'Imaging')->first();

        $tests = [
            [$blood?->id, 'Complete Blood Count (CBC)', 650, 'Blood', 'Same day', true, true],
            [$blood?->id, 'Lipid Profile', 1200, 'Blood', '24 hours', true, true],
            [$blood?->id, 'Blood Glucose Fasting', 300, 'Blood', 'Same day', true, true],
            [$urine?->id, 'Urine R/E', 350, 'Urine', 'Same day', false, true],
            [$hormone?->id, 'Thyroid Profile (T3, T4, TSH)', 1800, 'Blood', '24 hours', true, false],
            [$imaging?->id, 'ECG', 500, 'N/A', '30 minutes', true, false],
            [$imaging?->id, 'X-Ray Chest PA View', 900, 'N/A', '2 hours', false, false],
        ];

        foreach ($tests as [$categoryId, $name, $price, $sampleType, $delivery, $popular, $homeCollection]) {
            if (! $categoryId) {
                continue;
            }

            MedicalTest::firstOrCreate(['name' => $name], [
                'test_category_id' => $categoryId,
                'price' => $price,
                'sample_type' => $sampleType,
                'report_delivery_time' => $delivery,
                'instructions' => 'Please follow standard sample collection instructions.',
                'is_popular' => $popular,
                'home_collection_available' => $homeCollection,
            ]);
        }
    }

    private function seedAccounting(): void
    {
        $expenses = [
            ['title' => 'Lab Reagent Purchase', 'category' => 'Lab Supplies', 'amount' => 12500, 'expense_date' => now()->subDays(1)->toDateString()],
            ['title' => 'X-Ray Film Stock', 'category' => 'Imaging Supplies', 'amount' => 8400, 'expense_date' => now()->subDays(2)->toDateString()],
            ['title' => 'Utility Bill', 'category' => 'Operations', 'amount' => 5600, 'expense_date' => now()->toDateString()],
            ['title' => 'Cleaning Supplies', 'category' => 'Facility', 'amount' => 2200, 'expense_date' => now()->subDays(4)->toDateString()],
        ];

        foreach ($expenses as $expense) {
            Expense::firstOrCreate([
                'title' => $expense['title'],
                'expense_date' => $expense['expense_date'],
            ], $expense);
        }
    }

    private function seedStaff(): void
    {
        $staff = [
            ['email' => 'labtech@lancet.com', 'name' => 'Lab Technician', 'role' => 'Lab Technician', 'designation' => 'Senior Lab Technician', 'salary' => 28000],
            ['email' => 'accountant@lancet.com', 'name' => 'Accounts Officer', 'role' => 'Accountant', 'designation' => 'Accountant', 'salary' => 32000],
            ['email' => 'pathologist@lancet.com', 'name' => 'Dr. Pathologist', 'role' => 'Pathologist', 'designation' => 'Consultant Pathologist', 'salary' => 65000],
        ];

        foreach ($staff as $seed) {
            $user = User::firstOrCreate(['email' => $seed['email']], [
                'name' => $seed['name'],
                'password' => 'password',
                'status' => 'active',
            ]);
            $user->assignRole($seed['role']);

            Employee::firstOrCreate(['user_id' => $user->id], [
                'designation' => $seed['designation'],
                'salary' => $seed['salary'],
                'joining_date' => now()->subMonths(rand(2, 18))->toDateString(),
                'employment_status' => 'active',
            ]);
        }
    }

    private function seedInventory(): void
    {
        $supplier = Supplier::firstOrCreate(['name' => 'Medi Supply BD'], [
            'phone' => '01811000001',
            'email' => 'sales@medisupply.test',
            'address' => 'Dhaka Medical Market',
        ]);

        $items = [
            ['name' => 'CBC Reagent Kit', 'sku' => 'INV-CBC-001', 'category' => 'Reagent', 'stock' => 8, 'low_stock_threshold' => 10, 'unit_price' => 1450],
            ['name' => 'Vacutainer Tube', 'sku' => 'INV-TUBE-001', 'category' => 'Consumable', 'stock' => 250, 'low_stock_threshold' => 100, 'unit_price' => 18],
            ['name' => 'Urine Container', 'sku' => 'INV-URINE-001', 'category' => 'Consumable', 'stock' => 85, 'low_stock_threshold' => 100, 'unit_price' => 12],
            ['name' => 'X-Ray Film', 'sku' => 'INV-XRAY-001', 'category' => 'Imaging', 'stock' => 45, 'low_stock_threshold' => 25, 'unit_price' => 120],
        ];

        foreach ($items as $itemSeed) {
            $item = InventoryItem::firstOrCreate(['sku' => $itemSeed['sku']], $itemSeed);

            Purchase::firstOrCreate([
                'inventory_item_id' => $item->id,
                'invoice_ref' => 'PO-' . $itemSeed['sku'],
            ], [
                'supplier_id' => $supplier->id,
                'quantity' => 20,
                'unit_cost' => $itemSeed['unit_price'],
                'total_cost' => 20 * $itemSeed['unit_price'],
                'purchase_date' => now()->subDays(rand(1, 10))->toDateString(),
            ]);
        }
    }
}
