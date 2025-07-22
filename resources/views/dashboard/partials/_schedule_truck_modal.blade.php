<div class="modal fade" id="editScheduleTruckModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form method="POST" class="modal-content">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Sửa xe trong lịch</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body row g-3">
        <div class="col-md-4">
          <label class="form-label">Xe</label>
          <select name="truck_id" class="form-select">
            <option value="">—</option>
            @foreach(($trucks ?? []) as $t)
        <option value="{{ $t->id }}">{{ $t->truck_name }}</option>
      @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tài xế</label>
          <select name="driver_id" class="form-select">
            <option value="">—</option>
            @foreach(($drivers ?? []) as $d)
        <option value="{{ $d->id }}">{{ $d->name }}</option>
      @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Từ</label>
          <select name="from_location_id" class="form-select">
            <option value="">—</option>
            @foreach(($locations ?? []) as $loc)
        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
      @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Đến</label>
          <select name="to_location_id" class="form-select">
            <option value="">—</option>
            @foreach(($locations ?? []) as $loc)
        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
      @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Phụ xe</label>
          <input type="text" name="assistant" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Mô tả hàng</label>
          <textarea name="cargo_desc" rows="2" class="form-control"></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary">Lưu</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </form>
  </div>
</div>