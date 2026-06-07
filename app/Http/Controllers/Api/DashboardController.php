<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        return response()->json([
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'pending_reports' => Report::whereIn('status', ['processing', 'pending_approval'])->count(),
            'total_patients' => Patient::count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'due_payments' => Invoice::sum('due_amount'),
            'recent_reports' => Report::latest()->limit(5)->get(),
            'recent_payments' => Invoice::where('paid_amount', '>', 0)->latest()->limit(5)->get(),
            'recent_activities' => Activity::query()->latest()->limit(8)->get(),
        ]);
    }
}
