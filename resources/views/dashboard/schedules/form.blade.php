@extends('layouts.app', ['title' => isset($schedule) ? 'Sửa Lịch trình' : 'Thêm Lịch trình'])

@php
    use App\Models\Truck;
    use App\Models\Driver;
    use App\Models\Location;

    $trucks = $trucks ?? Truck::orderBy('truck_name')->get();
    $drivers = $drivers ?? Driver::orderBy('name')->get();
    $locations = $locations ?? Location::orderBy('name')->get();
@endphp

@section('content')
    <form action="{{ isset($schedule) ? route('schedules.update', $schedule) : route('schedules.store') }}" method="POST"
        class="card p-3 mb-4">
        @csrf
        @if(isset($schedule)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $schedule->title ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày</label>
            <input type="date" name="date" class="form-control"
                value="{{ old('date', optional($schedule->date ?? null)->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú chung</label>
            <textarea name="general_note" rows="2"
                class="form-control">{{ old('general_note', $schedule->general_note ?? '') }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary submit-once">{{ isset($schedule) ? 'Cập nhật' : 'Thêm mới' }}</button>
            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>

    @if(isset($schedule))
        {{-- Danh sách xe trong lịch --}}
        <div class="card mb-4">
            <div class="card-header"><strong>Xe trong lịch này</strong></div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Xe</th>
                            <th>Tài xế</th>
                            <th>Từ</th>
                            <th>Đến</th>
                            <th>Phụ xe</th>
                            <th>Hàng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedule->scheduleTrucks()->with(['truck', 'driver', 'fromLocation', 'toLocation'])->get() as $st)
                            <tr>
                                <td>{{ $st->id }}</td>
                                <td>{{ $st->truck?->truck_name ?? '—' }}</td>
                                <td>{{ $st->driver?->name ?? '—' }}</td>
                                <td>{{ $st->fromLocation?->name ?? '—' }}</td>
                                <td>{{ $st->toLocation?->name ?? '—' }}</td>
                                <td>{{ $st->assistant ?? '—' }}</td>
                                <td>{{ Str::limit($st->cargo_desc, 40) }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editScheduleTruckModal" data-id="{{ $st->id }}"
                                        data-truck="{{ $st->truck_id }}" data-driver="{{ $st->driver_id }}"
                                        data-from="{{ $st->from_location_id }}" data-to="{{ $st->to_location_id }}"
                                        data-assistant="{{ $st->assistant }}" data-cargo="{{ $st->cargo_desc }}">Sửa</button>
                                    <form action="{{ route('schedules.trucks.destroy', $st->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Xóa xe khỏi lịch?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Chưa có xe.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form thêm xe vào lịch --}}
        <div class="card mb-5">
            <div class="card-header"><strong>Thêm xe vào lịch</strong></div>
            <div class="card-body">
                <form action="{{ route('schedules.trucks.store', $schedule) }}" method="POST"
                    class="row gy-2 gx-2 align-items-end">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Xe</label>
                        <select name="truck_id" class="form-select" required>
                            <option value="">— Chọn —</option>
                            @foreach($trucks as $t)
                                <option value="{{ $t->id }}">{{ $t->truck_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tài xế</label>
                        <select name="driver_id" class="form-select">
                            <option value="">— Không —</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Từ</label>
                        <select name="from_location_id" class="form-select">
                            <option value="">—</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Đến</label>
                        <select name="to_location_id" class="form-select">
                            <option value="">—</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Phụ xe</label>
                        <input type="text" name="assistant" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả hàng</label>
                        <input type="text" name="cargo_desc" class="form-control" placeholder="VD: 101 con 3M (20 con...)">
                    </div>
                    <div class="col-12 mt-2">
                        <button class="btn btn-success btn-sm">+ Thêm xe</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal sửa schedule_truck --}}
        @include('dashboard.partials._schedule_truck_modal', [
            'trucks' => $trucks,
            'drivers' => $drivers,
            'locations' => $locations
        ])
    @endif
@endsection
    
   @push('scripts') 
 @if(isset($schedule))
    <script>
       document    .addEvent L istener('DOMContentLoaded',()=>{
        cons    t modalE l =document.getElementById('editScheduleTruckModal');
        if(!    modalEl) return;
                modalEl.addEventListener('show.bs.modal',e=>{
                    const btn=e.relatedTarget;
                    const id=btn.dataset.id;
                    modalEl.querySelector('form').action='{{ url('schedule-trucks') }}/'+id;
                  modalEl.querySelector('select[name="truck_id"]').value = btn.dataset.truck||'' ;
                modalEl.querySelector('select[name="driver_id"]').value=bt n .dataset.driver|| '' ;
                modalEl.querySelector('select[name="from_location_id"]').value=btn.dataset.from||'';
                modalEl.querySelector('select[name="to_location_id"]').value=btn.dataset.to||'';
            modalEl.querySelector('input[name="assistant"]').value=btn.dataset.assistant||'';
            modalEl.querySelector('textarea[name="cargo_desc"]').value=btn.dataset.cargo||'';
        });    });
    </script>
@endif
@endpush
