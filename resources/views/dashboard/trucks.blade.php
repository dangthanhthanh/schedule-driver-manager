@extends('layouts.app')

@section('title', $title ?? 'Xe tải')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Xe tải</h3>
    <a href="{{ route('trucks.create') }}" class="btn btn-primary submit-once">+ Thêm xe</a>
    </div>

    <form method="GET" class="row g-2 mb-3" id="truck-filter-form" data-endpoint="{{ route('trucks.index') }}">
    <div class="col-md-4">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Biển số...">
    </div>
    <div class="col-md-3">
    <input type="text" name="status" value="{{ request('status') }}" class="form-control" placeholder="Trạng thái...">
    </div>
    <div class="col-md-3">
    <input type="number" name="min_capacity" value="{{ request('min_capacity') }}" class="form-control"
      placeholder="Tải trọng tối thiểu">
    </div>
    <div class="col-md-2 d-grid">
    <button class="btn btn-secondary">Lọc</button>
    </div>
    </form>

    <div class="card">
    <div class="table-responsive">
    <table class="table table-sm table-hover align-middle mb-0" id="truck-table">
      <thead>
      <tr>
      <th>#</th>
      <th>Biển số</th>
      <th>Tải trọng</th>
      <th>Trạng thái hiện tại</th>
      <th>Số sàn</th>
      <th>TT</th>
      <th></th>
      </tr>
      </thead>
      <tbody>
      @foreach($trucks as $id => $t)
      <tr>
      <td>{{ $id + 1 }}</td>
      <td>{{ $t->truck_name }}</td>
      <td>{{ $t->capacity ?? '—' }}</td> 
      <td>{{ $t->status ?? '—' }}</td>
      <td>{{ $t->floor ?? '—' }}</td>
      <td>
      <a href="{{ route('trucks.status-log', $t) }}" class="btn btn-sm btn-outline-info">Log</a>
      </td>
      <td class="text-end">
      <a href="{{ route('trucks.edit', $t) }}" class="btn btn-sm btn-outline-warning">Sửa</a>
      <form action="{{ route('trucks.destroy', $t) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Xóa xe?')">
      @csrf @method('DELETE')
      <button class="btn btn-sm btn-outline-danger submit-once">Xóa</button>
      </form>
      </td>
      </tr>
    @endforeach
      </tbody>
    </table>
    </div>

    <div class="card-footer text-center" data-ajax-pager>
    {{ $trucks->links() }}
    </div>
    </div>
@endsection

@push('scripts')
    @include('dashboard.partials._ajax_helpers')

    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('truck-filter-form');
    const table = document.getElementById('truck-table');
    if (!form || !table) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      ajaxLoadTable({
      endpoint: form.dataset.endpoint,
      form: form,
      container: table,
      renderRow: function (item) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
      <td>${item.id}</td>
      <td>${item.truck_name ?? ''}</td>
      <td>${item.capacity ?? '—'}</td>
      <td>${item.status ?? '—'}</td>
      <td>${item.floor ?? '—'}</td>
      <td><a href="{{ url('trucks') }}/${item.id}/status-log" class="btn btn-sm btn-outline-info">Log</a></td>
      <td class="text-end">
        <a href="{{ url('trucks') }}/${item.id}/edit" class="btn btn-sm btn-outline-warning">Sửa</a>
        <form action="{{ url('trucks') }}/${item.id}" method="POST" class="d-inline" onsubmit="return confirm('Xóa xe?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger submit-once">Xóa</button>
        </form>
      </td>`;
      return tr;
      }
      });
    });
    });
    </script>
@endpush