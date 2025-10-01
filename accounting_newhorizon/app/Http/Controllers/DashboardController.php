<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Account;

class DashboardController extends Controller
{
    public function index()
    {
        // $totalRevenue = Transaction::where('type', 'revenue')->sum('amount');
        // $totalExpenses = Transaction::where('type', 'expense')->sum('amount');
        // $customersCount = Customer::count();
        // $netProfit = $totalRevenue - $totalExpenses;

        // // بيانات الرسوم البيانية
        // $monthlyLabels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        // $monthlyRevenue = [12000, 19000, 15000, 25000, 22000, 30000];
        // $monthlyExpenses = [8000, 12000, 10000, 18000, 15000, 20000];

        // $salesDistributionLabels = ['تذاكر طيران', 'حجوزات فنادق', 'تأشيرات', 'تأمين سفر', 'خدمات أخرى'];
        // $salesDistributionData = [45, 25, 15, 10, 5];

        // $recentTransactions = Transaction::with('account')
        //     ->orderBy('date', 'desc')
        //     ->take(5)
        //     ->get();
        return view('dashboard');
        // return view('dashboard', compact(
        //     'totalRevenue',
        //     'totalExpenses',
        //     'customersCount',
        //     'netProfit',
        //     'monthlyLabels',
        //     'monthlyRevenue',
        //     'monthlyExpenses',
        //     'salesDistributionLabels',
        //     'salesDistributionData',
        //     'recentTransactions'
        // ));
    }
}
