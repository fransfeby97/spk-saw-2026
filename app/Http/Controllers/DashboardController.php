<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Employee;
use App\Models\Period;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $criteriaCount = Criteria::count();
        $periodCount = Period::count();
        $activePeriod = Period::getActive();

        return view('dashboard', compact('employeeCount', 'criteriaCount', 'periodCount', 'activePeriod'));
    }
}
