@extends('layouts.app')

@php
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\ScheduleTruck;

$driverCount = Driver::count();
$truckCount = Truck::count();
$scheduleCount = Schedule::count();

$latestDrivers = Driver::with('truck')->latest()->limit(8)->get();
$latestTrucks = Truck::latest()->limit(8)->get();
$latestSchedules = Schedule::with('scheduleTrucks.truck', 'scheduleTrucks.driver')->latest()->get();

$today = Carbon::now()->startOfDay();
$days = CarbonPeriod::create($today, $today->copy()->addDays(4))->toArray();

$drivers = Driver::with('truck')->orderBy('name')->get();
$driverIds = $drivers->pluck('id')->all();
$trucks = Truck::orderBy('truck_name')->get();
$truckIds = $trucks->pluck('id')->all();

// Map schedule_truck by (driver_id, date) and (truck_id, date)
$driverCellMap = [];
$truckCellMap = [];

$allCells = ScheduleTruck::with(['schedule', 'truck', 'driver', 'toLocation'])
    ->whereHas('schedule', fn($q) => $q->whereBetween('date', [$today->toDateString(), $today->copy()->addDays(4)->toDateString()]))
    ->get();

foreach ($allCells as $st) {
    $date = optional($st->schedule)->date?->format('Y-m-d');
    if ($st->driver_id && $date)
        $driverCellMap[$st->driver_id][$date] = $st;
    if ($st->truck_id && $date)
        $truckCellMap[$st->truck_id][$date] = $st;
}
@endphp

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Tổng tài xế</h5>
                    <p class="fs-3 mb-0">{{ $driverCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Tổng xe tải</h5>
                    <p class="fs-3 mb-0">{{ $truckCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Tổng lịch trình</h5>
                    <p class="fs-3 mb-0">{{ $scheduleCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tài xế mới + Xe mới trên cùng 1 hàng --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <strong>Tài xế mới cập nhật</strong>
                    <a href="{{ route('drivers.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped m-0 align-middle">
                        <thead>
                            <tr>
                                <th class="text-muted">#</th>
                                <th class="text-muted">Tên</th>
                                <th class="text-muted">Điện thoại</th>
                                <th class="text-muted">Xe</th>
                                <th class="text-muted">Chính</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestDrivers as $id => $d)
                                <tr>
                                    <td>{{ $id + 1 }}</td>
                                    <td><a href="{{ route('drivers.edit', $d) }}">{{ $d->name }}</a></td>
                                    <td>{{ $d->phone }}</td>
                                    <td>{{ $d->truck->truck_name ?? '—' }}</td>
                                    <td>{!! $d->is_main_driver ? '<span class="badge bg-success">Chính</span>' : '<span class="badge bg-secondary">Phụ</span>' !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <strong>Xe tải mới cập nhật</strong>
                    <a href="{{ route('trucks.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped m-0 align-middle">
                        <thead>
                            <tr>
                                <th class="text-muted">#</th>
                                <th class="text-muted">biển số</th>
                                <th class="text-muted">Trạng thái</th>
                                <th class="text-muted">Dự án</th>
                                <th class="text-muted">Số sàn</th>
                                <th class="text-muted">Tải trọng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestTrucks as $id => $t)
                                <tr>
                                    <td>{{ $id + 1 }}</td>
                                    <td><a href="{{ route('trucks.edit', $t) }}">{{ $t->truck_name }}</a></td>
                                    <td>{{ $t->status ?? '—' }}</td>
                                    <td>{{ $t->project ?? '—' }}</td>
                                    <td>{{ $t->floor ?? '—' }}</td>
                                    <td>{{ $t->capacity ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Lịch trình gần đây --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <strong>Lịch chạy dự án</strong>
            <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        <th>Ngày</th>
                        <th>Số xe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestSchedules as $id => $s)
                        <tr>
                            <td>{{ $id + 1 }}</td>
                            <td><a href="{{ route('schedules.edit', $s) }}">{{ $s->title ?? '—' }}</a></td>
                            <td>{{ $s->date?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $s->scheduleTrucks->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        {{-- Lịch tài xế 5 ngày tới --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong>Lịch tài xế (5 ngày tới)</strong>
            <a href="{{ route('planning.drivers', ['start' => $today->toDateString(), 'days' => 5]) }}"
                class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered mb-0 align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-start">Tài xế</th>
                        @foreach($days as $d)
                            <th>{{ $d->format('d/m') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($drivers as $drv)
                        <tr>
                            <td class="text-start">{{ $drv->name }}</td>
                            @foreach($days as $d)
                                @php
                                    $dateKey = $d->format('Y-m-d');
                                    $cell = $driverCellMap[$drv->id][$dateKey] ?? null;
                                    $desc = $cell?->cargo_desc ?? '';
                                    $loc = $cell?->toLocation?->name ?? '';
                                    $truck = $cell?->truck?->truck_name ?? '';
                                @endphp
                                <td>
                                    @if($cell)
                                        số lượng: <span class="badge bg-info">{{ $desc }}</span>
                                        <div class="small text-muted">vị trí: {{ $loc }}</div>
                                        <div class="small">số xe {{ $truck }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        {{-- Lịch xe 5 ngày tới --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong>Lịch xe tải (5 ngày tới)</strong>
            <a href="{{ route('planning.trucks', ['start' => $today->toDateString(), 'days' => 5]) }}"
                class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered mb-5 align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-start">Xe</th>
                        @foreach($days as $d)
                            <th>{{ $d->format('d/m') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($trucks as $t)
                        <tr>
                            <td class="text-start">{{ $t->truck_name }}</td>
                            @foreach($days as $d)
                                @php
                                    $dateKey = $d->format('Y-m-d');
                                    $cell = $truckCellMap[$t->id][$dateKey] ?? null;
                                    $desc = $cell?->cargo_desc ?? '';
                                    $drv = $cell?->driver?->name ?? '';
                                    $loc = $cell?->toLocation?->name ?? '';
                                @endphp
                                <td>
                                    @if($cell)
                                        Tải: <span class="badge bg-info">{{ $desc }}</span>
                                        <div class="small text-muted">Tài: {{ $drv }}</div>
                                        <div class="small">Vị trí : {{ $loc }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection