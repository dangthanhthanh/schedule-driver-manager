<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Truck;
use App\Models\Schedule;

class DashboardController extends Controller
{
    public function index()
    {
        $driversCount   = Driver::count();
        $trucksCount    = Truck::count();
        $schedulesCount = Schedule::count();

        $latestDrivers   = Driver::with('truck')->latest()->limit(10)->get();
        $latestTrucks    = Truck::latest()->limit(10)->get();
        $latestSchedules = Schedule::with('scheduleTrucks.truck', 'scheduleTrucks.driver')->latest()->limit(10)->get();

        return view('dashboard.index', [
            'title'          => 'Dashboard',
            'driversCount'   => $driversCount,
            'trucksCount'    => $trucksCount,
            'schedulesCount' => $schedulesCount,
            'drivers'        => $latestDrivers,
            'trucks'         => $latestTrucks,
            'schedules'      => $latestSchedules,
        ]);
    }
}