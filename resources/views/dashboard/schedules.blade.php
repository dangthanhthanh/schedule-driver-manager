@extends('layouts.app')

@php
  use Illuminate\Support\Str;
  /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Schedule[] $schedules */
@endphp

@section('title', $title ?? 'Lịch trình')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Lịch trình</h3>
    <a href="{{ route('schedules.create') }}" class="btn btn-primary submit-once">+ Thêm lịch</a>
  </div>

  <form method="GET" class="row g-2 mb-3" id="schedule-filter-form" data-endpoint="{{ route('schedules.index') }}">
    <div class="col-md-4">
    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tiêu đề / ghi chú...">
    </div>
    <div class="col-md-3">
    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
    </div>
    <div class="col-md-2 d-grid">
    <button class="btn btn-secondary">Lọc</button>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
    <table class="table table-sm table-hover mb-0 align-middle" id="schedule-table">
      <thead>
      <tr>
        <th>#</th>
        <th>Tiêu đề</th>
        <th>Ngày</th>
        <th>Số xe</th>
        <th>Ghi chú</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      @foreach($schedules as $s)
      <tr>
      <td>{{ $s->id }}</td>
      <td>{{ $s->title ?? '—' }}</td>
      <td>{{ optional($s->date)->format('d/m/Y') ?? '—' }}</td>
      <td>{{ $s->schedule_trucks_count ?? $s->scheduleTrucks->count() }}</td>
      <td>{{ Str::limit($s->general_note, 40) }}</td>
      <td class="text-end">
      <a href="{{ route('schedules.edit', $s) }}" class="btn btn-sm btn-outline-warning">Sửa</a>
      <form action="{{ route('schedules.destroy', $s) }}" method="POST" class="d-inline"
        onsubmit="return confirm('Xóa lịch này?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger submit-once">Xóa</button>
      </form>
      <a href="{{ route('schedules.trucks', $s) }}" class="btn btn-sm btn-outline-info">Xe</a>
      </td>
      </tr>
    @endforeach
      </tbody>
    </table>
    </div>

    <div class="card-footer text-center" data-ajax-pager>
    {{ $schedules->links() }}
    </div>
  </div>
@endsection

@push('scripts')
  @include('dashboard.partials._ajax_helpers')

  <script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('schedule-filter-form');
    const table = document.getElementById('schedule-table');
    if (!form || !table) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      ajaxLoadTable({
      endpoint: form.dataset.endpoint,
      form: form,
      container: table,
      renderRow: function (item) {
        const date = item.date ?? '';
        const note = item.general_note ? item.general_note.substring(0, 40) + '…' : '';
        const count = item.schedule_trucks_count ?? (item.schedule_trucks ? item.schedule_trucks.length : '');
        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td>${item.id}</td>
        <td>${item.title ?? '—'}</td>
        <td>${date}</td>
        <td>${count}</td>
        <td>${note}</td>
        <td class="text-end">
          <a href="{{ url('schedules') }}/${item.id}/edit" class="btn btn-sm btn-outline-warning">Sửa</a>
          <form action="{{ url('schedules') }}/${item.id}" method="POST" class="d-inline" onsubmit="return confirm('Xóa lịch?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger submit-once">Xóa</button>
          </form>
          <a href="{{ url('schedules') }}/${item.id}/trucks" class="btn btn-sm btn-outline-info">Xe</a>
        </td>`;
        return tr;
      }
      });
    });
    });
  </script>
@endpush