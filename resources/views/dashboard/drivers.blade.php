@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Driver[] $drivers */
    use App\Models\Truck;
    $trucks = $trucks ?? Truck::orderBy('truck_name')->get();
@endphp

@section('title', $title ?? 'Tài xế')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Tài xế</h3>
    <a href="{{ route('drivers.create') }}" class="btn btn-primary submit-once">+ Thêm tài xế</a>
</div>

{{-- Filter --}}
<form method="GET" class="row g-2 mb-3" id="driver-filter-form" data-endpoint="{{ route('drivers.index') }}">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm tên / SĐT...">
    </div>
    <div class="col-md-3">
        <select name="truck_id" class="form-select">
            <option value="">-- Xe --</option>
            @foreach($trucks as $t)
                <option value="{{ $t->id }}" @selected(request('truck_id') == $t->id)>
                    {{ $t->truck_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="is_main_driver" class="form-select">
            <option value="">-- Chính/Phụ --</option>
            <option value="1" @selected(request('is_main_driver')==='1')>Chính</option>
            <option value="0" @selected(request('is_main_driver')==='0')>Phụ</option>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-secondary">Lọc</button>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" id="driver-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>SĐT</th>
                    <th>Xe</th>
                    <th>Chính</th>
                    <th>TT</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($drivers as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->phone }}</td>
                    <td>{{ $d->truck?->truck_name ?? '—' }}</td>
                    <td>{!! $d->is_main_driver ? '<span class="badge bg-success">Chính</span>' : '<span class="badge bg-secondary">Phụ</span>' !!}</td>
                    <td>
                        <a href="{{ route('drivers.status-log', $d) }}" class="btn btn-sm btn-outline-info">Log</a>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('drivers.edit', $d) }}" class="btn btn-sm btn-outline-warning">Sửa</a>
                        <form action="{{ route('drivers.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài xế?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger submit-once">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Laravel pagination fallback --}}
    <div class="card-footer text-center" data-ajax-pager>
        {{ $drivers->links() }}
    </div>
</div>
@endsection

@push('scripts')
@include('dashboard.partials._ajax_helpers')

<script>
document.addEventListener('DOMContentLoaded',()=>{
    const form = document.getElementById('driver-filter-form');
    const table = document.getElementById('driver-table');
    if(!form || !table) return;

    form.addEventListener('submit',function(e){
        e.preventDefault();
        ajaxLoadTable({
            endpoint: form.dataset.endpoint,
            form: form,
            container: table,
            renderRow: function(item){
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.name ?? ''}</td>
                    <td>${item.phone ?? ''}</td>
                    <td>${item.truck?.truck_name ?? '—'}</td>
                    <td>${item.is_main_driver ? '<span class="badge bg-success">Chính</span>' : '<span class="badge bg-secondary">Phụ</span>'}</td>
                    <td><a href="{{ url('drivers') }}/${item.id}/status-log" class="btn btn-sm btn-outline-info">Log</a></td>
                    <td class="text-end">
                        <a href="{{ url('drivers') }}/${item.id}/edit" class="btn btn-sm btn-outline-warning">Sửa</a>
                        <form action="{{ url('drivers') }}/${item.id}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài xế?')">
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
