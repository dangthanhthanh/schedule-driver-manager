@extends('layouts.app', ['title' => isset($driver) ? 'Sửa Tài xế' : 'Thêm Tài xế'])

@php
  use App\Models\Truck;
  $trucks = $trucks ?? Truck::orderBy('truck_name')->get();
@endphp

@section('content')
  <form action="{{ isset($driver) ? route('drivers.update', $driver) : route('drivers.store') }}" method="POST"
    class="card p-3">
    @csrf
    @if(isset($driver)) @method('PUT') @endif

    <div class="mb-3">
    <label class="form-label">Tên tài xế</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $driver->name ?? '') }}" required>
    </div>

    <div class="mb-3">
    <label class="form-label">Số điện thoại</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $driver->phone ?? '') }}">
    </div>

    <div class="mb-3">
    <label class="form-label">Xe đang gán</label>
    <select name="truck_id" class="form-select">
      <option value="">— Không —</option>
      @foreach($trucks as $t)
      <option value="{{ $t->id }}" @selected(old('truck_id', $driver->truck_id ?? '') == $t->id)>{{ $t->truck_name }}
      </option>
    @endforeach
    </select>
    </div>

    <div class="form-check mb-3">
    <input type="checkbox" id="is_main_driver" name="is_main_driver" class="form-check-input" value="1" {{ old('is_main_driver', $driver->is_main_driver ?? false) ? 'checked' : '' }}>
    <label for="is_main_driver" class="form-check-label">Lái chính</label>
    </div>

    <div class="d-flex gap-2">
    <button class="btn btn-primary submit-once">{{ isset($driver) ? 'Cập nhật' : 'Thêm mới' }}</button>
    <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
@endsection