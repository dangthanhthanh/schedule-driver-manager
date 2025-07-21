@extends('layouts.app')

@section('title', $title ?? 'Địa điểm')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Địa điểm</h3>
    <a href="{{ route('locations.create') }}" class="btn btn-primary submit-once">+ Thêm địa điểm</a>
  </div>

  <form method="GET" class="row g-2 mb-3" id="location-filter-form" data-endpoint="{{ route('locations.index') }}">
    <div class="col-md-5">
    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tên / Địa chỉ...">
    </div>
    <div class="col-md-2 d-grid">
    <button class="btn btn-secondary">Lọc</button>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
    <table class="table table-sm table-hover mb-0 align-middle" id="location-table">
      <thead>
      <tr>
        <th>#</th>
        <th>Tên</th>
        <th>Địa chỉ</th>
        <th>Link</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      @foreach($locations as $l)
      <tr>
        <td>{{ $l->id }}</td>
        <td>{{ $l->name }}</td>
        <td>{{ $l->address ?? '—' }}</td>
        <td>
        @if($l->link)
      <a href="{{ $l->link }}" target="_blank" rel="noopener">Mở</a>
      @else —
      @endif
        </td>
        <td class="text-end">
        <a href="{{ route('locations.edit', $l) }}" class="btn btn-sm btn-outline-warning">Sửa</a>
        <form action="{{ route('locations.destroy', $l) }}" method="POST" class="d-inline"
        onsubmit="return confirm('Xóa địa điểm?')">
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
    {{ $locations->links() }}
    </div>
  </div>
@endsection

@push('scripts')
  @include('dashboard.partials._ajax_helpers')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('location-filter-form');
    const table = document.getElementById('location-table');
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
            <td>${item.name ?? ''}</td>
            <td>${item.address ?? '—'}</td>
            <td>${item.link ? '<a href="' + item.link + '" target="_blank" rel="noopener">Mở</a>' : '—'}</td>
            <td class="text-end">
              <a href="{{ url('locations') }}/${item.id}/edit" class="btn btn-sm btn-outline-warning">Sửa</a>
              <form action="{{ url('locations') }}/${item.id}" method="POST" class="d-inline" onsubmit="return confirm('Xóa địa điểm?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger submit-once">Xóa</button>
              </form>
            </td>
          `;
        return tr;
      }
      });
    });
    });
  </script>
@endpush