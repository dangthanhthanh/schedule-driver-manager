<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Truck;
use App\Models\Schedule;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'title' => 'Dashboard',
            'driversCount' => Driver::count(),
            'trucksCount' => Truck::count(),
            'schedulesCount' => Schedule::count(),
            'drivers' => Driver::with('truck')->latest()->limit(20)->get(),
            'trucks' => Truck::latest()->limit(20)->get(),
            'schedules' => Schedule::with('scheduleTrucks.truck', 'scheduleTrucks.driver')->latest()->limit(20)->get(),
        ]);
    }
}
