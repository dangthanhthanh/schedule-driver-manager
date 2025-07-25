<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\{
    Driver,
    Truck,
    Location,
    Schedule,
    ScheduleTruck
};

class PlanningController extends Controller
{
    protected function wantsJson(Request $r): bool
    {
        return $r->ajax() || $r->wantsJson();
    }

    protected function resolveDateRange(Request $r): array
    {
        $start = $r->input('start');
        $days  = (int)($r->input('days', 7));
        if (!$start) $start = now()->toDateString();

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate   = (clone $startDate)->addDays($days - 1)->endOfDay();
        return [$startDate, $endDate];
    }

    protected function makeDays(Carbon $start, Carbon $end): array
    {
        $days = [];
        foreach (CarbonPeriod::create($start, $end) as $d) {
            $days[] = $d->copy();
        }
        return $days;
    }

    protected function fetchCellMap(string $mode, array $ids, Carbon $start, Carbon $end): array
    {
        $rel = ScheduleTruck::with(['schedule','truck','driver','toLocation'])
            ->whereHas('schedule', fn($q)=>$q->whereBetween('date', [$start->toDateString(), $end->toDateString()]));

        if ($mode === 'driver') {
            $rel->whereIn('driver_id', $ids);
        } elseif ($mode === 'truck') {
            $rel->whereIn('truck_id', $ids);
        }

        $rows = $rel->get();

        $map = []; // [id][Y-m-d] => ScheduleTruck
        foreach ($rows as $st) {
            // Avoid IDE warning by breaking up the optional chain
            $schedule = $st->schedule;
            $date = ($schedule && $schedule->date) ? $schedule->date->format('Y-m-d') : null;
            if (!$date) continue;
            $key = $mode === 'driver' ? $st->driver_id : $st->truck_id;
            if (!$key) continue;
            $map[$key][$date] = $st;
        }
        return $map;
    }

    protected function ensureScheduleForDate(string $date): Schedule
    {
        return Schedule::firstOrCreate(
            ['date' => $date],
            ['title' => 'AUTO '.$date, 'general_note' => null]
        );
    }

    public function driversGrid(Request $request)
    {
        [$start,$end] = $this->resolveDateRange($request);
        $days         = $this->makeDays($start,$end);

        $drivers = Driver::with('truck')->orderBy('name')->get();
        $driverIds = $drivers->pluck('id')->all();
        $cellMap = $this->fetchCellMap('driver', $driverIds, $start, $end);

        if ($this->wantsJson($request)) {
            $html = view('dashboard.planning.drivers_grid_table', compact('drivers','days','cellMap'))->render();
            return response()->json(['html'=>$html]);
        }

        return view('dashboard.planning.drivers_grid', [
            'title'    => 'Lịch theo ngày (Tài xế)',
            'drivers'  => $drivers,
            'days'     => $days,
            'cellMap'  => $cellMap,
            'start'    => $start->toDateString(),
            'daysCount'=> $end->diffInDays($start)+1,
        ]);
    }

    public function saveDriverCell(Request $request)
    {
        $data = $request->validate([
            'driver_id'  => 'required|exists:drivers,id',
            'date'       => 'required|date',
            'truck_id'   => 'nullable|exists:trucks,id',
            'location_id'=> 'nullable|exists:locations,id',
            'pickup_time'=> 'nullable',
            'status'     => 'nullable|string|max:100',
            'cargo_desc' => 'nullable|string',
            'assistant'  => 'nullable|string|max:255',
            'schedule_truck_id' => 'nullable|exists:schedule_trucks,id',
        ]);
        $schedule = $this->ensureScheduleForDate($data['date']);

        if (!empty($data['schedule_truck_id'])) {
            $st = ScheduleTruck::findOrFail($data['schedule_truck_id']);
            $st->update([
                'schedule_id'    => $schedule->id,
                'truck_id'       => $data['truck_id'] ?? null,
                'driver_id'      => $data['driver_id'],
                'to_location_id' => $data['location_id'] ?? null,
                'assistant'      => $data['assistant'] ?? null,
                'cargo_desc'     => $this->buildCargoField($data),
            ]);
        } else {
            $st = ScheduleTruck::create([
                'schedule_id'    => $schedule->id,
                'truck_id'       => $data['truck_id'] ?? null,
                'driver_id'      => $data['driver_id'],
                'to_location_id' => $data['location_id'] ?? null,
                'assistant'      => $data['assistant'] ?? null,
                'cargo_desc'     => $this->buildCargoField($data),
            ]);
        }

        return response()->json([
            'message' => 'Đã lưu lịch.',
            'cell'    => $this->cellPayload($st),
        ]);
    }

    public function deleteDriverCell(Request $request)
    {
        $id = $request->input('schedule_truck_id');
        if (!$id) return response()->json(['message'=>'Thiếu ID'],422);
        $st = ScheduleTruck::findOrFail($id);
        $st->delete();
        return response()->json(['message'=>'Đã xoá lịch.']);
    }

    public function trucksGrid(Request $request)
    {
        [$start,$end] = $this->resolveDateRange($request);
        $days         = $this->makeDays($start,$end);

        $trucks   = Truck::orderBy('truck_name')->get();
        $truckIds = $trucks->pluck('id')->all();
        $cellMap = $this->fetchCellMap('truck', $truckIds, $start, $end);

        if ($this->wantsJson($request)) {
            $html = view('dashboard.planning.trucks_grid_table', compact('trucks','days','cellMap'))->render();
            return response()->json(['html'=>$html]);
        }

        return view('dashboard.planning.trucks_grid', [
            'title'    => 'Lịch theo ngày (Xe)',
            'trucks'   => $trucks,
            'days'     => $days,
            'cellMap'  => $cellMap,
            'start'    => $start->toDateString(),
            'daysCount'=> $end->diffInDays($start)+1,
        ]);
    }

    public function saveTruckCell(Request $request)
    {
        $data = $request->validate([
            'truck_id'   => 'required|exists:trucks,id',
            'date'       => 'required|date',
            'driver_id'  => 'nullable|exists:drivers,id',
            'location_id'=> 'nullable|exists:locations,id',
            'pickup_time'=> 'nullable',
            'status'     => 'nullable|string|max:100',
            'cargo_desc' => 'nullable|string',
            'assistant'  => 'nullable|string|max:255',
            'schedule_truck_id' => 'nullable|exists:schedule_trucks,id',
        ]);
        $schedule = $this->ensureScheduleForDate($data['date']);

        if (!empty($data['schedule_truck_id'])) {
            $st = ScheduleTruck::findOrFail($data['schedule_truck_id']);
            $st->update([
                'schedule_id'    => $schedule->id,
                'truck_id'       => $data['truck_id'],
                'driver_id'      => $data['driver_id'] ?? null,
                'to_location_id' => $data['location_id'] ?? null,
                'assistant'      => $data['assistant'] ?? null,
                'cargo_desc'     => $this->buildCargoField($data),
            ]);
        } else {
            $st = ScheduleTruck::create([
                'schedule_id'    => $schedule->id,
                'truck_id'       => $data['truck_id'],
                'driver_id'      => $data['driver_id'] ?? null,
                'to_location_id' => $data['location_id'] ?? null,
                'assistant'      => $data['assistant'] ?? null,
                'cargo_desc'     => $this->buildCargoField($data),
            ]);
        }

        return response()->json([
            'message' => 'Đã lưu lịch.',
            'cell'    => $this->cellPayload($st),
        ]);
    }

    public function deleteTruckCell(Request $request)
    {
        $id = $request->input('schedule_truck_id');
        if (!$id) return response()->json(['message'=>'Thiếu ID'],422);
        $st = ScheduleTruck::findOrFail($id);
        $st->delete();
        return response()->json(['message'=>'Đã xoá lịch.']);
    }

    public function transportGrid(Request $request)
    {
        [$start,$end] = $this->resolveDateRange($request);
        $days         = $this->makeDays($start,$end);

        $drivers = Driver::with('truck')->orderBy('name')->get();
        $driverIds = $drivers->pluck('id')->all();
        $cellMap = $this->fetchCellMap('driver', $driverIds, $start, $end);

        if ($this->wantsJson($request)) {
            $html = view('dashboard.planning.transport_grid_table', compact('drivers','days','cellMap'))->render();
            return response()->json(['html'=>$html]);
        }

        return view('dashboard.planning.transport_grid', [
            'title'    => 'Lịch vận chuyển',
            'drivers'  => $drivers,
            'days'     => $days,
            'cellMap'  => $cellMap,
            'start'    => $start->toDateString(),
            'daysCount'=> $end->diffInDays($start)+1,
        ]);
    }

    public function saveTransportCell(Request $request)
    {
        return $this->saveDriverCell($request);
    }

    public function deleteTransportCell(Request $request)
    {
        return $this->deleteDriverCell($request);
    }

    protected function buildCargoField(array $data): ?string
    {
        $txt = trim($data['cargo_desc'] ?? '');
        $parts = [];
        if (!empty($data['status']))     $parts[] = $data['status'];
        if (!empty($data['pickup_time']))$parts[] = 'Giờ: '.$data['pickup_time'];
        if ($txt !== '')                 $parts[] = $txt;
        return $parts ? implode(' | ', $parts) : null;
    }

    protected function cellPayload(ScheduleTruck $st): array
    {
        $st->loadMissing(['truck','driver','toLocation','schedule']);
        return [
            'id'         => $st->id,
            'date'       => $st->schedule && $st->schedule->date
            ? $st->schedule->date->toDateString()
            : null,
            'truck'      => $st->truck?->truck_name,
            'driver'     => $st->driver?->name,
            'location'   => $st->toLocation?->name,
            'assistant'  => $st->assistant,
            'cargo_desc' => $st->cargo_desc,
        ];
    }
}