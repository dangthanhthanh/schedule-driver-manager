@extends('layouts.app', ['title' => isset($truck) ? 'Sửa Xe tải' : 'Thêm Xe tải'])

@section('content')
 <form action="{{ isset($truck) ? route('trucks.update', $truck) : route('trucks.store') }}" method="POST"
  class="card p-3">
  @csrf
  @if(isset($truck)) @method('PUT') @endif

  <div class="mb-3">
   <label class="form-label">Tên xe</label>
   <input type="text" name="truck_name" class="form-control" value="{{ old('truck_name', $truck->truck_name ?? '') }}"
    required>
  </div>

  <div class="mb-3">
   <label class="form-label">Trạng thái</label>
   <input type="text" name="status" class="form-control" value="{{ old('status', $truck->status ?? '') }}">
  </div>

  <div class="row">
   <div class="col-md-4 mb-3">
    <label class="form-label">Dự án</label>
    <input type="text" name="project" class="form-control" value="{{ old('project', $truck->project ?? '') }}">
   </div>
   <div class="col-md-4 mb-3">
    <label class="form-label">Bãi/tầng</label>
    <input type="number" name="floor" class="form-control" value="{{ old('floor', $truck->floor ?? '') }}">
   </div>
   <div class="col-md-4 mb-3">
    <label class="form-label">Tải trọng (kg)</label>
    <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $truck->capacity ?? '') }}">
   </div>
  </div>

  <div class="mb-3">
   <label class="form-label">Mô tả</label>
   <textarea name="description" rows="3" class="form-control">{{ old('description', $truck->description ?? '') }}</textarea>
  </div>

  <div class="d-flex gap-2">
   <button class="btn btn-primary">{{ isset($truck) ? 'Cập nhật' : 'Thêm mới' }}</button>
   <a href="{{ route('trucks.index') }}" class="btn btn-secondary">Hủy</a>
  </div>
 </form>
@endsection