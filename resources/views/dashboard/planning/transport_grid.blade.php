@extends('layouts.app')

@section('title', $title ?? 'Lịch vận chuyển')

@section('content')
 <h3 class="mb-3">Lịch vận chuyển (Driver + Cargo)</h3>

 <form id="transport-grid-filter" class="row g-2 mb-3" data-endpoint="{{ route('planning.transport') }}">
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
 </form>

 <div id="transport-grid-wrapper">
  @include('dashboard.planning.transport_grid_table', ['drivers' => $drivers, 'days' => $days, 'cellMap' => $cellMap])
 </div>

 @include('dashboard.planning.modal_cell', ['mode' => 'driver']) {{-- dùng form driver --}}
@endsection

@push('scripts')
 @include('dashboard.planning._grid_scripts', ['mode' => 'driver', 'transport' => true])
@endpush

@push('head')
 @include('dashboard.planning._grid_styles')
@endpush