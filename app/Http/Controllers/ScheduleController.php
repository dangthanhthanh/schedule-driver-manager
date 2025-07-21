<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleTruck;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Location;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected function wantsJson(Request $r): bool
    {
        return $r->wantsJson() || $r->is('api/*');
    }

    /* INDEX */
    public function index(Request $request)
    {
        $schedules = Schedule::withCount('scheduleTrucks')
            ->orderByDesc('date')
            ->paginate(25);

        if ($this->wantsJson($request)) {
            return $schedules;
        }

        return view('dashboard.schedules', [
            'title'     => 'Lịch trình',
            'schedules' => $schedules,
        ]);
    }

    /* CREATE */
    public function create()
    {
        return view('dashboard.schedules.form', [
            'title'     => 'Thêm lịch trình',
            'trucks'    => Truck::get(),
            'drivers'   => Driver::get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    /* STORE */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'nullable|string|max:255',
            'date'         => 'required|date',
            'general_note' => 'nullable|string',
        ]);

        $schedule = Schedule::create($data);

        if ($this->wantsJson($request)) {
            return response()->json(['message'=>'Schedule created','schedule'=>$schedule],201);
        }

        return redirect()->route('schedules.edit',$schedule)->with('success','Lịch trình đã được thêm.');
    }

    /* SHOW */
    public function show(Request $request, Schedule $schedule)
    {
        $schedule->load([
            'scheduleTrucks.truck',
            'scheduleTrucks.driver',
            'scheduleTrucks.fromLocation',
            'scheduleTrucks.toLocation',
        ]);

        if ($this->wantsJson($request)) {
            return $schedule;
        }

        // web: chuyển về edit page (hiển thị chi tiết + form trucks)
        return redirect()->route('schedules.edit',$schedule);
    }

    /* EDIT */
    public function edit(Schedule $schedule)
    {
        return view('dashboard.schedules.form', [
            'title'     => 'Chỉnh sửa lịch trình',
            'schedule'  => $schedule,
            'trucks'    => Truck::get(),
            'drivers'   => Driver::get(),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    /* UPDATE */
    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'title'        => 'nullable|string|max:255',
            'date'         => 'required|date',
            'general_note' => 'nullable|string',
        ]);

        $schedule->update($data);

        if ($this->wantsJson($request)) {
            return response()->json(['message'=>'Schedule updated','schedule'=>$schedule]);
        }

        return redirect()->route('schedules.edit',$schedule)->with('success','Cập nhật lịch trình thành công.');
    }

    /* DESTROY */
    public function destroy(Request $request, Schedule $schedule)
    {
        $schedule->delete();

        if ($this->wantsJson($request)) {
            return response()->json(['message'=>'Schedule deleted']);
        }

        return redirect()->route('schedules.index')->with('success','Đã xóa lịch trình.');
    }

    /* =================================================
     | ScheduleTrucks management
     * ================================================= */

    public function getScheduleTrucks(Request $request, Schedule $schedule)
    {
        $items = $schedule->scheduleTrucks()
            ->with(['truck','driver','fromLocation','toLocation'])
            ->get();

        if ($this->wantsJson($request)) {
            return $items;
        }

        // Web: render partial table? Tạm quay lại edit page
        return redirect()->route('schedules.edit',$schedule);
    }

    public function addTruckToSchedule(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'truck_id'         => 'required|exists:trucks,id',
            'driver_id'        => 'nullable|exists:drivers,id',
            'from_location_id' => 'nullable|exists:locations,id',
            'to_location_id'   => 'nullable|exists:locations,id',
            'assistant'        => 'nullable|string|max:255',
            'cargo_desc'       => 'nullable|string',
        ]);

        $item = $schedule->scheduleTrucks()->create($data);

        if ($this->wantsJson($request)) {
            return response()->json([
                'message'=>'Truck added to schedule',
                'item'=>$item->load(['truck','driver','fromLocation','toLocation']),
            ],201);
        }

        return back()->with('success','Đã thêm xe vào lịch.');
    }

    public function updateTruckInSchedule(Request $request, ScheduleTruck $scheduleTruck)
    {
        $data = $request->validate([
            'truck_id'         => 'nullable|exists:trucks,id',
            'driver_id'        => 'nullable|exists:drivers,id',
            'from_location_id' => 'nullable|exists:locations,id',
            'to_location_id'   => 'nullable|exists:locations,id',
            'assistant'        => 'nullable|string|max:255',
            'cargo_desc'       => 'nullable|string',
        ]);

        $scheduleTruck->update($data);

        if ($this->wantsJson($request)) {
            return response()->json([
                'message'=>'Schedule truck updated',
                'item'=>$scheduleTruck->load(['truck','driver','fromLocation','toLocation']),
            ]);
        }

        return back()->with('success','Đã cập nhật xe trong lịch.');
    }

    public function removeTruckFromSchedule(Request $request, ScheduleTruck $scheduleTruck)
    {
        $scheduleTruck->delete();

        if ($this->wantsJson($request)) {
            return response()->json(['message'=>'Schedule truck deleted']);
        }

        return back()->with('success','Đã xóa xe khỏi lịch.');
    }
}
