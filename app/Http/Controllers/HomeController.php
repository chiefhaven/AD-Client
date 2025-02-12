<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;
// use DB;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get counts for each qualification type
        $education_levels = Employee::select('education_level', DB::raw('count(*) as total'))
            ->where('client_id', Auth::user()->client_id)
            ->groupBy('education_level')
            ->get();

        $monthly_billings = DB::table('billings')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->where('client_id', Auth::user()->client_id)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        $billingsCount = Billing::where('client_id', Auth::user()->client_id)->get()->count();

        $employees = Employee::with('leave')->where('client_id', Auth::user()->client_id)->get();

        $employeesCount = $employees->count();

        $payrollCount = Payroll::where('client_id', Auth::user()->client_id)->get()->count();

        $leaveCount = $employees->pluck('leave')->flatten()->count();

        $client = client::find(Auth::user()->client_id);

        // Pass the data to the view
        return view('home', compact('client', 'education_levels','monthly_billings', 'billingsCount', 'employeesCount', 'payrollCount', 'leaveCount'));
    }

}
