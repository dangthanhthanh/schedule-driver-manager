@php use Illuminate\Support\Str; @endphp
<div class="planning-grid-wrapper">
 <table class="planning-grid table table-bordered table-sm">
  <thead>
   <tr>
    <th class="sticky-col">STT</th>
    <th class="sticky-col sticky-col-2">BIỂN SỐ XE</th>
    <th class="sticky-col sticky-col-3">TẢI TRỌNG</th>
    <th class="sticky-col sticky-col-4">MÔ TẢ</th>
    <th class="sticky-col sticky-col-5">SỐ TÀI XẾ</th>
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
  <th>TRẠNG THÁI</th>
  <th>TÀI XẾ</th>
  <th>GIỜ</th>
 @endforeach
   </tr>
  </thead>
  <tbody>
   @foreach($trucks as $idx => $t)
    <tr data-truck="{{ $t->id }}">
  <td class="sticky-col">{{ $idx + 1 }}</td>
  <td class="sticky-col sticky-col-2">{{ $t->truck_name }}</td>
  <td class="sticky-col sticky-col-3">{{ $t->capacity ?? '—' }}</td>
  <td class="sticky-col sticky-col-4">{{ Str::limit($t->description, 20) }}</td>
  <td class="sticky-col sticky-col-5">{{ $t->drivers()->count() }}</td>
  @foreach($days as $d)
    @php
    $dateKey = $d->format('Y-m-d');
    $cell = $cellMap[$t->id][$dateKey] ?? null;
    $txt = $cell?->cargo_desc;
    $drvName = $cell?->driver?->name;
    $time = null;
    if ($txt && preg_match('/Giờ:\s*([0-9:]+)/u', $txt, $m))
  $time = $m[1];
    @endphp
    <td class="{{ $cell ? 'cell-has' : 'cell-empty' }}" data-date="{{ $dateKey }}" data-field="status"
  data-cell="{{ $cell?->id ?? '' }}" title="Click để sửa">
  {!! $cell ? e(Str::limit($txt, 12)) : '<span class="text-muted">+</span>' !!}
    </td>
    <td class="{{ $cell ? 'cell-has' : 'cell-empty' }}" data-date="{{ $dateKey }}" data-field="driver"
  data-cell="{{ $cell?->id ?? '' }}" title="Click để sửa">
  {!! $cell ? e($drvName ?? '') : '<span class="text-muted">+</span>' !!}
    </td>
    <td class="{{ $cell ? 'cell-has' : 'cell-empty' }}" data-date="{{ $dateKey }}" data-field="time"
  data-cell="{{ $cell?->id ?? '' }}" title="Click để sửa">
  {!! $time ? e($time) : ($cell ? '' : '<span class="text-muted">+</span>') !!}
    </td>
  @endforeach
    </tr>
   @endforeach
  </tbody>
 </table>
</div>