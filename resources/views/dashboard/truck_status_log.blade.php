@extends('layouts.app')

@php
  use App\Models\Truck;
  use App\Models\TruckStatus;

  $isPerTruck = isset($truck);
  $statuses = $statuses ?? TruckStatus::orderBy('name')->get();
  $trucks = $trucks ?? ($isPerTruck ? collect([$truck]) : Truck::orderBy('truck_name')->get());
@endphp

@section('title', $title ?? ($isPerTruck ? "Trạng thái xe: {$truck->truck_name}" : 'Nhật ký trạng thái xe'))

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">
    @if($isPerTruck) Trạng thái xe: {{ $truck->truck_name }} @else Nhật ký trạng thái xe @endif
    </h3>
    <a href="{{ route('trucks.index') }}" class="btn btn-secondary btn-sm">← Danh sách xe</a>
  </div>

  {{-- Filter --}}
  <form method="GET" id="truck-log-filter-form" class="row g-2 mb-3"
    data-endpoint="{{ $isPerTruck ? route('trucks.status-log', $truck) : (Route::has('trucks.status_log') ? route('trucks.status_log') : route('trucks.index')) }}">
    @unless($isPerTruck)
    <div class="col-md-3">
    <select name="truck_id" class="form-select">
      <option value="">-- Tất cả xe --</option>
      @foreach($trucks as $t)
      <option value="{{ $t->id }}" @selected(request('truck_id') == $t->id)>{{ $t->truck_name }}</option>
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
    <table class="table table-sm table-hover align-middle mb-0" id="truck-log-table">
      <thead>
      <tr>
        @unless($isPerTruck)<th>Xe</th>@endunless
        <th>Trạng thái</th>
        <th>Ngày</th>
        <th>Thời lượng</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      @foreach($logs as $log)
      <tr>
      @unless($isPerTruck)<td>{{ $log->truck->truck_name ?? '—' }}</td>@endunless
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
  @if($isPerTruck)
    <div class="card mt-4">
    <div class="card-header"><strong>Ghi trạng thái mới cho {{ $truck->truck_name }}</strong></div>
    <div class="card-body">
    <form action="{{ route('trucks.status-log.store', $truck) }}" method="POST" class="row g-2 align-items-end">
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
    const form = document.getElementById('truck-log-filter-form');
    const table = document.getElementById('truck-log-table');
    if (!form || !table) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      ajaxLoadTable({
      endpoint: form.dataset.endpoint,
      form: form,
      container: table,
      renderRow: function (item) {
        const tr = document.createElement('tr');
        @unless($isPerTruck)
      const truckName = item.truck?.truck_name ?? '—';
      tr.innerHTML += `<td>${truckName}</td>`;
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