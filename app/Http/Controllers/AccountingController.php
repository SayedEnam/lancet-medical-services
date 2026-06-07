<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AccountingController extends Controller
{
    public function summary(Request $request)
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $incomeToday = Invoice::whereDate('created_at', $today)->sum('paid_amount');
        $expenseToday = Expense::whereDate('expense_date', $today)->sum('amount');

        $incomeMonth = Invoice::whereBetween('created_at', [$monthStart, now()])->sum('paid_amount');
        $expenseMonth = Expense::whereBetween('expense_date', [$monthStart->toDateString(), now()->toDateString()])->sum('amount');

        return response()->json([
            'income_today' => (float) $incomeToday,
            'expense_today' => (float) $expenseToday,
            'net_today' => (float) ($incomeToday - $expenseToday),
            'income_month' => (float) $incomeMonth,
            'expense_month' => (float) $expenseMonth,
            'net_month' => (float) ($incomeMonth - $expenseMonth),
            'recent_expenses' => Expense::latest('expense_date')->limit(10)->get(),
            'recent_invoices' => Invoice::with('patient')->latest()->limit(10)->get(),
        ]);
    }
}
