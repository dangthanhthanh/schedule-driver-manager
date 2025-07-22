@php
 $today = now()->format('Y-m-d');
@endphp
<div class="planning-grid-wrapper">
 <table class="planning-grid table table-bordered table-sm">
  <thead>
   <tr>
    <th class="sticky-col">STT</th>
    <th class="sticky-col sticky-col-2">BIỂN SỐ XE</th>
    <th class="sticky-col sticky-col-3">HỌ VÀ TÊN</th>
    <th class="sticky-col sticky-col-4">SĐT</th>
    <th class="sticky-col sticky-col-5">TẢI TRỌNG</th>
    @foreach($days as $d)
  <th colspan="3" class="bg-info-subtle">{{ $d->format('d/m/Y') }}</th>
 @endforeach
   </tr>
   <tr>
    <th class="sticky-col"></th>
    <th class="sticky-col sticky-col-2"></th>
    <th class="sticky-col sticky-col-3"></th>
    <th class="sticky-col sticky-col-4"></th>
    <th class="sticky-col sticky-col-5"></th>
    @foreach($days as $d)
  <th>TÌNH TRẠNG</th>
  <th>TRẠI/KH</th>
  <th>GIỜ</th>
 @endforeach
   </tr>
  </thead>
  <tbody>
   @foreach($drivers as $idx => $drv)
    <tr data-driver="{{ $drv->id }}">
  <td class="sticky-col">{{ $idx + 1 }}</td>
  <td class="sticky-col sticky-col-2">{{ $drv->truck?->truck_name ?? '—' }}</td>
  <td class="sticky-col sticky-col-3">{{ $drv->name }}</td>
  <td class="sticky-col sticky-col-4">{{ $drv->phone }}</td>
  <td class="sticky-col sticky-col-5">{{ $drv->truck?->capacity ?? '—' }}</td>
  @foreach($days as $d)
    @php
    $dateKey = $d->format('Y-m-d');
    $cell = $cellMap[$drv->id][$dateKey] ?? null;
    $status = $cell?->cargo_desc;
    $loc = $cell?->toLocation?->name;
    $cellColor = '';
    // Nếu nghỉ phép/bảo trì, đổi màu nền và icon
    if ($status && preg_match('/Nghỉ phép/', $status))
  $cellColor = 'bg-danger-subtle border border-danger';
    elseif ($status && preg_match('/Bảo trì/', $status))
  $cellColor = 'bg-warning-subtle border border-warning';
    $time = null;
    if ($status && preg_match('/Giờ:\s*([0-9:]+)/u', $status, $m)) {
  $time = $m[1];
    }
    @endphp
    <td
  class="{{ $cell ? 'cell-has' : 'cell-empty' }} {{ $dateKey === $today ? 'bg-light border border-primary' : '' }} {{ $cellColor }}"
  data-date="{{ $dateKey }}" data-field="status" data-cell="{{ $cell?->id ?? '' }}"
  title="{{ $cell ? $status : 'Click để thêm' }}">
  @if($cell)
   <span class="badge text-truncate" style="max-width:110px;">
    @if($cellColor) <i class="bi bi-exclamation-triangle"></i> @endif
    {{ \Illuminate\Support\Str::limit($status, 12) }}
   </span>
  @else
   <span class="text-muted">+</span>
  @endif
    </td>
    <td
  class="{{ $cell ? 'cell-has' : 'cell-empty' }} {{ $dateKey === $today ? 'bg-light border border-primary' : '' }}"
  data-date="{{ $dateKey }}" data-field="loc" data-cell="{{ $cell?->id ?? '' }}"
  title="{{ $cell ? $loc : 'Click để thêm' }}">
  @if($cell)
   <span class="badge text-truncate" style="max-width:110px;">{{ $loc }}</span>
  @else
   <span class="text-muted">+</span>
  @endif
    </td>
    <td
  class="{{ $cell ? 'cell-has' : 'cell-empty' }} {{ $dateKey === $today ? 'bg-light border border-primary' : '' }}"
  data-date="{{ $dateKey }}" data-field="time" data-cell="{{ $cell?->id ?? '' }}"
  title="{{ $cell && $time ? $time : 'Click để thêm' }}">
  @if($time)
   <span class="badge">{{ $time }}</span>
  @else
   {{ $cell ? '' : '+ ' }}
  @endif
    </td>
  @endforeach
    </tr>
   @endforeach
  </tbody>
 </table>
</div>