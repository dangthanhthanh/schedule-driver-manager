@extends('layouts.app')

@php
    use App\Models\Driver;
    use App\Models\Truck;
    use App\Models\Schedule;

    $driverCount = Driver::count();
    $truckCount = Truck::count();
    $scheduleCount = Schedule::count();

    $latestDrivers = Driver::with('truck')->latest()->get();
    $latestTrucks = Truck::latest()->get();
    $latestSchedules = Schedule::with('scheduleTrucks.truck','scheduleTrucks.driver')->latest()->get();
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

{{-- Tài xế gần đây --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <strong>Tài xế mới cập nhật</strong>
        <a href="{{ route('drivers.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead><tr><th>#</th><th>Tên</th><th>Điện thoại</th><th>Xe</th><th>Chính</th></tr></thead>
            <tbody>
                @foreach($latestDrivers as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td><a href="{{ route('drivers.edit',$d) }}">{{ $d->name }}</a></td>
                        <td>{{ $d->phone }}</td>
                        <td>{{ $d->truck->truck_name ?? '—' }}</td>
                        <td>{!! $d->is_main_driver ? '<span class="badge bg-success">Chính</span>' : '<span class="badge bg-secondary">Phụ</span>' !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Xe tải gần đây --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <strong>Xe tải mới cập nhật</strong>
        <a href="{{ route('trucks.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead><tr><th>#</th><th>Tên xe</th><th>TT</th><th>Dự án</th><th>Tải trọng</th></tr></thead>
            <tbody>
                @foreach($latestTrucks as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td><a href="{{ route('trucks.edit',$t) }}">{{ $t->truck_name }}</a></td>
                        <td>{{ $t->status ?? '—' }}</td>
                        <td>{{ $t->project ?? '—' }}</td>
                        <td>{{ $t->capacity ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Lịch trình gần đây --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <strong>Lịch trình gần đây</strong>
        <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead><tr><th>#</th><th>Tiêu đề</th><th>Ngày</th><th>Số xe</th></tr></thead>
            <tbody>
                @foreach($latestSchedules as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td><a href="{{ route('schedules.edit',$s) }}">{{ $s->title ?? '—' }}</a></td>
                        <td>{{ $s->date?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $s->scheduleTrucks->count() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
