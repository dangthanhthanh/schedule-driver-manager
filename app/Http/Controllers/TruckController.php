<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use App\Models\TruckStatus;
use App\Models\TruckStatusLog;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    protected function wantsJson(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->is('api/*');
    }

    /* =========================================================
     | Resource: INDEX
     * ========================================================= */
    public function index(Request $request)
    {
        $query = Truck::query();

        // Optional filters
        if ($request->filled('q')) {
            $q =  $request->input('q');
            $query->where('truck_name', 'like', "%{$q}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', $request->input('min_capacity'));
        }

        $trucks = $query->withCount('drivers')->paginate(25)->withQueryString();

        if ($this->wantsJson($request)) {
            return $trucks;
        }

        return view('dashboard.trucks', [
            'title'  => 'Xe tải',
            'trucks' => $trucks,
        ]);
    }

    /* =========================================================
     | Resource: CREATE (form)
     * ========================================================= */
    public function create()
    {
        return view('dashboard.trucks.form', [
            'title' => 'Thêm xe tải',
        ]);
    }

    /* =========================================================
     | Resource: STORE
     * ========================================================= */
    public function store(Request $request)
    {
        $data = $request->validate([
            'truck_name'  => 'required|string|max:255|unique:trucks,truck_name',
            'status'      => 'nullable|string|max:50',
            'project_id'  => 'nullable|integer',
            'floor'       => 'nullable|integer',
            'capacity'    => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $truck = Truck::create($data);

        if ($this->wantsJson($request)) {
            return response()->json(['message' => 'Truck created.', 'truck' => $truck], 201);
        }

        return redirect()->route('trucks.index')->with('success', 'Xe tải đã được thêm.');
    }

    /* =========================================================
     | Resource: SHOW
     * ========================================================= */
    public function show(Request $request, Truck $truck)
    {
        $truck->load('drivers', 'statusLogs.status');

        if ($this->wantsJson($request)) {
            return $truck;
        }

        // Web: chuyển sang trang log trạng thái xe
        return redirect()->route('trucks.status-log', $truck);
    }

    /* =========================================================
     | Resource: EDIT (form)
     * ========================================================= */
    public function edit(Truck $truck)
    {
        return view('dashboard.trucks.form', [
            'title' => 'Chỉnh sửa xe tải',
            'truck' => $truck,
        ]);
    }

    /* =========================================================
     | Resource: UPDATE
     * ========================================================= */
    public function update(Request $request, Truck $truck)
    {
        $data = $request->validate([
            'truck_name'  => 'required|string|max:255|unique:trucks,truck_name,' . $truck->id,
            'status'      => 'nullable|string|max:50',
            'project_id'  => 'nullable|integer',
            'floor'       => 'nullable|integer',
            'capacity'    => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $truck->update($data);

        if ($this->wantsJson($request)) {
            return response()->json(['message' => 'Truck updated.', 'truck' => $truck]);
        }

        return redirect()->route('trucks.index')->with('success', 'Cập nhật xe tải thành công.');
    }

    /* =========================================================
     | Resource: DESTROY
     * ========================================================= */
    public function destroy(Request $request, Truck $truck)
    {
        $truck->delete();

        if ($this->wantsJson($request)) {
            return response()->json(['message' => 'Truck deleted.']);
        }

        return redirect()->route('trucks.index')->with('success', 'Đã xóa xe tải.');
    }

    /* =========================================================
     | Lookup: Truck Status danh mục
     * ========================================================= */
    public function getAvailableStatuses(Request $request)
    {
        $statuses = TruckStatus::orderBy('name')->get();

        if ($this->wantsJson($request)) {
            return $statuses;
        }

        return response()->json($statuses);
    }

    /* =========================================================
     | LOG: per-truck status log (GET)
     * Route: trucks/{truck}/status-log
     * Optional filters: date_from, date_to, status_id
     * ========================================================= */
    public function getStatusLog(Request $request, Truck $truck)
    {
        $query = $truck->statusLogs()->with('status')->orderByDesc('date');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        $logs = $query->paginate(50)->withQueryString();

        if ($this->wantsJson($request)) {
            return $logs;
        }

        return view('dashboard.truck_status_log', [
            'title'    => "Trạng thái xe: {$truck->truck_name}",
            'truck'    => $truck,
            'logs'     => $logs,
            'statuses' => TruckStatus::orderBy('name')->get(),
        ]);
    }

    /* =========================================================
     | LOG: add truck status (POST)
     * ========================================================= */
    public function updateStatusLog(Request $request, Truck $truck)
    {
        $data = $request->validate([
            'status_id' => 'required|exists:truck_statuses,id',
            'date'      => 'required|date',
            'time_unit' => 'required|numeric|min:0.5|max:1',
        ]);

        $log = $truck->statusLogs()->create($data);

        if ($this->wantsJson($request)) {
            return response()->json(['message' => 'Truck status logged.', 'log' => $log->load('status')]);
        }

        return back()->with('success', 'Đã ghi trạng thái xe.');
    }

    /* =========================================================
     | OPTIONAL: Global truck status log view (if needed)
     | /trucks/status-log?status_id=&date_from=&date_to=
     * ========================================================= */
    public function statusLog(Request $request)
    {
        // Only used if you add a separate "global" log route (not per-truck).
        $query = TruckStatusLog::with(['truck','status'])->orderByDesc('date');

        if ($request->filled('truck_id')) {
            $query->where('truck_id',  $request->input('truck_id'));
        }
        if ($request->filled('status_id')) {
            $query->where('status_id',  $request->input('status_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date','>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date','<=', $request->input('date_to'));
        }

        $logs = $query->paginate(50)->withQueryString();

        if ($this->wantsJson($request)) {
            return $logs;
        }

        return view('dashboard.truck_status_log', [
            'title'    => 'Nhật ký trạng thái xe',
            'logs'     => $logs,
            'statuses' => TruckStatus::orderBy('name')->get(),
            'trucks'   => Truck::get(),
        ]);
    }
}
