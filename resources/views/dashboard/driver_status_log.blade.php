@extends('layouts.app')

@php
  use App\Models\Driver;
  use App\Models\DriverStatus;

  $isPerDriver = isset($driver);
  $statuses = $statuses ?? DriverStatus::orderBy('name')->get();
  $drivers = $drivers ?? ($isPerDriver ? collect([$driver]) : Driver::orderBy('name')->get());
@endphp

@section('title', $title ?? ($isPerDriver ? "Trạng thái: {$driver->name}" : 'Nhật ký trạng thái tài xế'))

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">
    @if($isPerDriver) Trạng thái: {{ $driver->name }} @else Nhật ký trạng thái tài xế @endif
    </h3>
    <a href="{{ route('drivers.index') }}" class="btn btn-secondary btn-sm">← Danh sách tài xế</a>
  </div>

  {{-- Filter --}}
  <form method="GET" id="driver-log-filter-form" class="row g-2 mb-3"
    data-endpoint="{{ $isPerDriver ? route('drivers.status-log', $driver) : (Route::has('drivers.status_log') ? route('drivers.status_log') : route('drivers.index')) }}">
    @unless($isPerDriver)
    <div class="col-md-3">
    <select name="driver_id" class="form-select">
      <option value="">-- Tất cả tài xế --</option>
      @foreach($drivers as $d)
      <option value="{{ $d->id }}" @selected(request('driver_id') == $d->id)>{{ $d->name }}</option>
    @endforeach
    </select>
    </div>
    @endunless
    <div class="col-md-3">
    <select name="status_id" class="form-select">
      <option value="">-- Trạng thái --</option>
      @foreach($statuses as $st)
      <option value="{{ $st->id }}" @selected(request('status_id') == $st->id)>{{ $st->name }}</option>
    @endforeach
    </select>
    </div>
    <div class="col-md-2">
    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="col-md-2">
    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="col-md-2 d-grid">
    <button class="btn btn-secondary">Lọc</button>
    </div>
  </form>

  {{-- Table --}}
  <div class="card">
    <div class="table-responsive">
    <table class="table table-sm table-hover align-middle mb-0" id="driver-log-table">
      <thead>
      <tr>
        @unless($isPerDriver)<th>Tài xế</th>@endunless
        <th>Trạng thái</th>
        <th>Ngày</th>
        <th>Thời lượng</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      @foreach($logs as $log)
      <tr>
      @unless($isPerDriver)<td>{{ $log->driver->name ?? '—' }}</td>@endunless
      <td>
      <span class="badge" style="background-color:{{ $log->status->color ?? '#999' }}">
        {{ $log->status->name ?? '—' }}
      </span>
      </td>
      <td>{{ $log->date->format('d/m/Y') }}</td>
      <td>{{ $log->time_unit }}</td>
      <td class="text-end text-muted small">{{ $log->created_at->format('d/m H:i') }}</td>
      </tr>
    @endforeach
      </tbody>
    </table>
    </div>

    <div class="card-footer text-center" data-ajax-pager>
    {{ $logs->links() }}
    </div>
  </div>

  {{-- Add new log --}}
  @if($isPerDriver)
    <div class="card mt-4">
    <div class="card-header"><strong>Ghi trạng thái mới cho {{ $driver->name }}</strong></div>
    <div class="card-body">
    <form action="{{ route('drivers.status-log.store', $driver) }}" method="POST" class="row g-2 align-items-end">
      @csrf
      <div class="col-md-4">
      <label class="form-label">Ngày</label>
      <input type="date" name="date" class="form-control" required value="{{ now()->format('Y-m-d') }}">
      </div>
      <div class="col-md-4">
      <label class="form-label">Trạng thái</label>
      <select name="status_id" class="form-select" required>
      <option value="">-- Chọn --</option>
      @foreach($statuses as $st)
      <option value="{{ $st->id }}">{{ $st->name }}</option>
      @endforeach
      </select>
      </div>
      <div class="col-md-2">
      <label class="form-label">Thời lượng</label>
      <select name="time_unit" class="form-select" required>
      <option value="1">1 ngày</option>
      <option value="0.5">Nửa ngày</option>
      </select>
      </div>
      <div class="col-md-2 d-grid">
      <button class="btn btn-primary submit-once">Ghi</button>
      </div>
    </form>
    </div>
    </div>
  @endif
@endsection

@push('scripts')
  @include('dashboard.partials._ajax_helpers')

  <script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('driver-log-filter-form');
    const table = document.getElementById('driver-log-table');
    if (!form || !table) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      ajaxLoadTable({
      endpoint: form.dataset.endpoint,
      form: form,
      container: table,
      renderRow: function (item) {
        const tr = document.createElement('tr');
        @unless($isPerDriver)
      const driverName = item.driver?.name ?? '—';
      tr.innerHTML += `<td>${driverName}</td>`;
      @endunless
          const color = item.status?.color ?? '#999';
        const statusName = item.status?.name ?? '—';
        const date = item.date ?? '';
        const timeUnit = item.time_unit ?? '';
        const created = item.created_at ?? '';
        tr.innerHTML += `
            <td><span class="badge" style="background-color:${color}">${statusName}</span></td>
            <td>${date}</td>
            <td>${timeUnit}</td>
            <td class="text-end text-muted small">${created}</td>`;
        return tr;
      }
      });
    });
    });
  </script>
@endpush