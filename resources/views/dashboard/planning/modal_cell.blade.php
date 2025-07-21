@php
 // $mode = 'driver' hoặc 'truck'
 $saveRoute = $mode === 'truck' ? route('planning.trucks.cell.save') : route('planning.drivers.cell.save');
 $delRoute = $mode === 'truck' ? route('planning.trucks.cell.delete') : route('planning.drivers.cell.delete');
@endphp

<div class="modal fade" id="planningCellModal" tabindex="-1" aria-labelledby="planningCellLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-lg">
  <div class="modal-content">
   <form id="planningCellForm">
    @csrf
    <input type="hidden" name="schedule_truck_id" id="cell_id">
    <input type="hidden" name="date" id="cell_date">
    @if($mode === 'driver')
  <input type="hidden" name="driver_id" id="cell_driver">
 @else
  <input type="hidden" name="truck_id" id="cell_truck">
 @endif

    <div class="modal-header">
     <h5 class="modal-title" id="planningCellLabel">Cập nhật lịch</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
    </div>
    <div class="modal-body row g-3">
     @if($mode === 'truck')
   <div class="col-md-6">
    <label class="form-label">Tài xế</label>
    <select name="driver_id" id="cell_driver_sel" class="form-select">
     <option value="">-- Không --</option>
     @foreach(\App\Models\Driver::orderBy('name')->get() as $d)
   <option value="{{ $d->id }}">{{ $d->name }}</option>
  @endforeach
    </select>
   </div>
  @else
   <div class="col-md-6">
    <label class="form-label">Xe</label>
    <select name="truck_id" id="cell_truck_sel" class="form-select">
     <option value="">-- Không --</option>
     @foreach(\App\Models\Truck::orderBy('truck_name')->get() as $t)
   <option value="{{ $t->id }}">{{ $t->truck_name }}</option>
  @endforeach
    </select>
   </div>
  @endif

     <div class="col-md-6">
      <label class="form-label">Trại / Khách hàng</label>
      <select name="location_id" id="cell_location" class="form-select">
       <option value="">-- Chọn --</option>
       @foreach(\App\Models\Location::orderBy('name')->get() as $loc)
     <option value="{{ $loc->id }}">{{ $loc->name }}</option>
    @endforeach
      </select>
     </div>

     <div class="col-md-4">
      <label class="form-label">Giờ nhận heo</label>
      <input type="time" name="pickup_time" id="cell_pickup_time" class="form-control">
     </div>

     <div class="col-md-4">
      <label class="form-label">Tình trạng</label>
      <input type="text" name="status" id="cell_status" class="form-control" placeholder="Đang chạy / Nghỉ...">
     </div>

     <div class="col-md-4">
      <label class="form-label">Phụ xe</label>
      <input type="text" name="assistant" id="cell_assistant" class="form-control">
     </div>

     <div class="col-12">
      <label class="form-label">Mô tả hàng (Cargo)</label>
      <textarea name="cargo_desc" id="cell_cargo" rows="2" class="form-control"></textarea>
     </div>
    </div>
    <div class="modal-footer justify-content-between">
     <button type="button" class="btn btn-outline-danger" id="cell_delete_btn" style="display:none;">Xóa</button>
     <div>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      <button type="submit" class="btn btn-primary submit-once" id="cell_save_btn">Lưu</button>
     </div>
    </div>
   </form>
  </div>
 </div>
</div>

<script>
 window.planningCell = {
  saveUrl: @json($saveRoute),
  delUrl: @json($delRoute),
  mode: @json($mode),
 };
</script>