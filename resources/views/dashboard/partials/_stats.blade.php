<div class="row g-3 mb-4">
 <div class="col-md-4">
  <div class="card text-center">
   <div class="card-body">
    <div class="fw-bold">Tài xế</div>
    <div class="fs-4">{{ $drivers->count() }}</div>
   </div>
  </div>
 </div>
 <div class="col-md-4">
  <div class="card text-center">
   <div class="card-body">
    <div class="fw-bold">Xe tải</div>
    <div class="fs-4">{{ $trucks->count() }}</div>
   </div>
  </div>
 </div>
 <div class="col-md-4">
  <div class="card text-center">
   <div class="card-body">
    <div class="fw-bold">Lịch trình</div>
    <div class="fs-4">{{ $schedules->count() }}</div>
   </div>
  </div>
 </div>
</div>