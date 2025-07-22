<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Truck;
use App\Models\DriverStatus;
use App\Models\DriverStatusLog;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Determine if response should be JSON.
     */
    protected function wantsJson(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->is('api/*');
    }

    /**
     * List drivers with optional filters.
     */
    public function index(Request $request)
    {
        $query = Driver::with('truck');
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }
        if ($request->filled('truck_id')) {
            $query->where('truck_id', $request->input('truck_id'));
        }
        if ($request->filled('is_main_driver')) {
            $query->where('is_main_driver', (bool) $request->input('is_main_driver'));
        }

        $drivers = $query->paginate(25)->withQueryString();

        if ($this->wantsJson($request)) return $drivers;

        return view('dashboard.drivers', [
            'title'   => 'Tài xế',
            'drivers' => $drivers,
            'trucks'  => Truck::get(),
        ]);
    }

    /**
     * Form create driver.
     */
    public function create()
    {
        return view('dashboard.drivers.form', [
            'title'  => 'Thêm tài xế',
            'trucks' => Truck::get(),
        ]);
    }

    /**
     * Store new driver.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'truck_id'       => 'nullable|exists:trucks,id',
            'is_main_driver' => 'boolean',
        ]);
        $data['is_main_driver'] = $request->boolean('is_main_driver');
        $driver = Driver::create($data);

        if ($this->wantsJson($request)) return response()->json(['message' => 'Driver created.', 'driver' => $driver], 201);

        return redirect()->route('drivers.index')->with('success', 'Tài xế đã được thêm.');
    }

    /**
     * Show driver detail (redirect to log).
     */
    public function show(Request $request, Driver $driver)
    {
        $driver->load('truck', 'statusLogs.status');
        if ($this->wantsJson($request)) return $driver;
        return redirect()->route('drivers.status-log', $driver);
    }

    /**
     * Form edit driver.
     */
    public function edit(Driver $driver)
    {
        return view('dashboard.drivers.form', [
            'title'  => 'Chỉnh sửa tài xế',
            'driver' => $driver,
            'trucks' => Truck::get(),
        ]);
    }

    /**
     * Update driver info.
     */
    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'truck_id'       => 'nullable|exists:trucks,id',
            'is_main_driver' => 'boolean',
        ]);
        $data['is_main_driver'] = $request->boolean('is_main_driver');
        $driver->update($data);

        if ($this->wantsJson($request)) return response()->json(['message' => 'Driver updated.', 'driver' => $driver]);
        return redirect()->route('drivers.index')->with('success', 'Cập nhật tài xế thành công.');
    }

    /**
     * Delete driver.
     */
    public function destroy(Request $request, Driver $driver)
    {
        $driver->delete();
        if ($this->wantsJson($request)) return response()->json(['message' => 'Driver deleted.']);
        return redirect()->route('drivers.index')->with('success', 'Đã xóa tài xế.');
    }

    /**
     * Get driver status lookup (for AJAX).
     */
    public function getAvailableStatuses(Request $request)
    {
        $statuses = DriverStatus::orderBy('name')->get();
        if ($this->wantsJson($request)) return $statuses;
        return response()->json($statuses);
    }

    /**
     * Get driver status logs (filter by date/status).
     */
    public function getStatusLog(Request $request, Driver $driver)
    {
        $query = $driver->statusLogs()->with('status')->orderByDesc('date');
        if ($request->filled('date_from')) $query->whereDate('date', '>=', $request->input('date_from'));
        if ($request->filled('date_to'))   $query->whereDate('date', '<=', $request->input('date_to'));
        if ($request->filled('status_id')) $query->where('status_id', $request->input('status_id'));

        $logs = $query->paginate(50)->withQueryString();

        if ($this->wantsJson($request)) return $logs;

        return view('dashboard.driver_status_log', [
            'title'    => "Trạng thái: {$driver->name}",
            'driver'   => $driver,
            'logs'     => $logs,
            'statuses' => DriverStatus::orderBy('name')->get(),
        ]);
    }

    /**
     * Add a driver status log.
     */
    public function updateStatusLog(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'status_id' => 'required|exists:driver_statuses,id',
            'date'      => 'required|date',
            'time_unit' => 'required|numeric|min:0.5|max:1',
        ]);
        $log = $driver->statusLogs()->create($data);

        if ($this->wantsJson($request)) return response()->json(['message' => 'Driver status logged.', 'log' => $log->load('status')]);
        return back()->with('success', 'Đã ghi trạng thái tài xế.');
    }
}