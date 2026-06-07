<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MedicalTestController;
use App\Http\Controllers\NotificationLogController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestOrderController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/reports/verify/{token}', [ReportController::class, 'verifyQr']);
Route::get('/public-settings', [SettingController::class, 'publicBranding']);

Route::get('/reports/{id}/download', [ReportController::class, 'downloadPdf'])->name('reports.download');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/accounting/summary', [AccountingController::class, 'summary']);

    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'store']);
    Route::post('/settings/logo', [SettingController::class, 'uploadLogo']);

    Route::apiResource('patients', PatientController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('test-categories', TestCategoryController::class);
    Route::apiResource('medical-tests', MedicalTestController::class);
    Route::apiResource('test-orders', TestOrderController::class);
    Route::apiResource('test-results', TestResultController::class);
    Route::apiResource('reports', ReportController::class);
    Route::post('/reports/{id}/status', [ReportController::class, 'setStatus']);
    Route::post('/reports/{id}/generate-pdf', [ReportController::class, 'generatePdf']);


    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('inventory-items', InventoryItemController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('prescriptions', PrescriptionController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('notification-logs', NotificationLogController::class);
});
