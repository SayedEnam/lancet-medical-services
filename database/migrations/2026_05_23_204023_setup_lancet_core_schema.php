<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('password');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('patient_id')->unique();
            $table->string('photo_path')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('medical_history')->nullable();
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('specialization');
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->json('visiting_schedule')->nullable();
            $table->boolean('is_available')->default(true);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['scheduled', 'checked_in', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('is_walk_in')->default(false);
            $table->unsignedInteger('queue_no')->nullable();
            $table->text('notes')->nullable();
        });

        Schema::table('test_categories', function (Blueprint $table) {
            $table->string('name')->unique();
            $table->text('description')->nullable();
        });

        Schema::table('medical_tests', function (Blueprint $table) {
            $table->foreignId('test_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('sample_type')->nullable();
            $table->string('report_delivery_time')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('home_collection_available')->default(false);
        });

        Schema::table('test_orders', function (Blueprint $table) {
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('barcode')->nullable();
            $table->enum('status', ['sample_collected', 'processing', 'completed', 'verified', 'delivered'])->default('sample_collected');
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pathologist_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('test_results', function (Blueprint $table) {
            $table->foreignId('test_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_test_id')->constrained()->cascadeOnDelete();
            $table->text('result_value')->nullable();
            $table->string('normal_range')->nullable();
            $table->text('remarks')->nullable();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('test_order_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['hematology', 'biochemistry', 'serology', 'radiology', 'histopathology']);
            $table->enum('status', ['processing', 'pending_approval', 'approved', 'delivered'])->default('processing');
            $table->string('pdf_path')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('digital_signature')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no')->unique();
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->enum('status', ['paid', 'partial', 'due'])->default('due');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['cash', 'card', 'bkash', 'nagad', 'rocket', 'bank_transfer']);
            $table->string('transaction_no')->nullable();
            $table->timestamp('paid_at')->useCurrent();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->longText('diagnosis_notes')->nullable();
            $table->longText('medicines')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('pdf_path')->nullable();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('designation');
            $table->decimal('salary', 12, 2)->default(0);
            $table->date('joining_date')->nullable();
            $table->enum('employment_status', ['active', 'inactive', 'on_leave'])->default('active');
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['sms', 'email', 'whatsapp']);
            $table->string('subject')->nullable();
            $table->text('message');
            $table->enum('status', ['sent', 'failed'])->default('sent');
        });
    }

    public function down(): void
    {
        // Simplified rollback for scaffold stage.
    }
};
