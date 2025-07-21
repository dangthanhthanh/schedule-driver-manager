@extends('layouts.app')

@section('title', $title ?? 'Lịch theo ngày (Xe)')

@section('content')
 <h3 class="mb-3">Lịch theo ngày (Xe)</h3>

 <form id="trucks-grid-filter" class="row g-2 mb-3" data-endpoint="{{ route('planning.trucks') }}">
  <div class="col-md-3">
   <label class="form-label">Bắt đầu</label>
   <input type="date" name="start" class="form-control" value="{{ $start }}">
  </div>
  <div class="col-md-2">
   <label class="form-label">Số ngày</label>
   <select name="days" class="form-select">
    @foreach([3, 7, 14, 30] as $opt)
  <option value="{{ $opt }}" @selected($daysCount == $opt)>{{ $opt }} ngày</option>
    @endforeach
   </select>
  </div>
  <div class="col-md-2 d-grid">
   <button class="btn btn-secondary mt-auto">Xem</button>
  </div>
  <div class="col-md-5 text-end">
   <a href="{{ route('planning.drivers') }}" class="btn btn-outline-primary btn-sm mt-4">Lịch tài xế</a>
   <a href="{{ route('planning.transport') }}" class="btn btn-outline-primary btn-sm mt-4">Lịch vận chuyển</a>
  </div>
 </form>

 <div id="trucks-grid-wrapper">
  @include('dashboard.planning.trucks_grid_table', ['trucks' => $trucks, 'days' => $days, 'cellMap' => $cellMap])
 </div>

 @include('dashboard.planning.modal_cell', ['mode' => 'truck'])
@endsection

@push('scripts')
 @include('dashboard.planning._grid_scripts', ['mode' => 'truck'])
@endpush

@push('head')
 @include('dashboard.planning._grid_styles')
@endpush