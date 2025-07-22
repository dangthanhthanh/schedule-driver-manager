@extends('layouts.app', ['title' => isset($location) ? 'Sửa Địa điểm' : 'Thêm Địa điểm'])

@section('content')
  <form action="{{ isset($location) ? route('locations.update', $location) : route('locations.store') }}" method="POST"
    class="card p-3">
    @csrf
    @if(isset($location)) @method('PUT') @endif

    <div class="mb-3">
    <label class="form-label">Tên</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $location->name ?? '') }}" required>
    </div>

    <div class="mb-3">
    <label class="form-label">Địa chỉ</label>
    <input type="text" name="address" class="form-control" value="{{ old('address', $location->address ?? '') }}">
    </div>

    <div class="mb-3">
    <label class="form-label">Link (Google Maps,...)</label>
    <input type="url" name="link" class="form-control" value="{{ old('link', $location->link ?? '') }}">
    </div>

    <div class="d-flex gap-2">
    <button class="btn btn-primary submit-once">{{ isset($location) ? 'Cập nhật' : 'Thêm mới' }}</button>
    <a href="{{ route('locations.index') }}" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
@endsection