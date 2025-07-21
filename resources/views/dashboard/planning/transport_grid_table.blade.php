@php use Illuminate\Support\Str; @endphp
<div class="planning-grid-wrapper">
 <table class="planning-grid table table-bordered table-sm">
  <thead>
   <tr>
    <th class="sticky-col">STT</th>
    <th class="sticky-col sticky-col-2">BIỂN SỐ XE</th>
    <th class="sticky-col sticky-col-3">TÀI XẾ</th>
    <th class="sticky-col sticky-col-4">SĐT</th>
    <th class="sticky-col sticky-col-5">TẢI TRỌNG</th>
    @foreach($days as $d)
  <th colspan="3" class="bg-success-subtle">{{ $d->format('d/m/Y') }}</th>
 @endforeach
   </tr>
   <tr>
    <th class="sticky-col"></th>
    <th class="sticky-col sticky-col-2"></th>
    <th class="sticky-col sticky-col-3"></th>
    <th class="sticky-col sticky-col-4"></th>
    <th class="sticky-col sticky-col-5"></th>
    @foreach($days as $d)
  <th>CARGO</th>
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
    $cargo = $cell?->cargo_desc;
    $loc = $cell?->toLocation?->name;
    $time = null;
    if ($cargo && preg_match('/Giờ:\s*([0-9:]+)/u', $cargo, $m))
  $time = $m[1];
    @endphp
    <td class="{{ $cell ? 'cell-has' : 'cell-empty' }}" data-date="{{ $dateKey }}" data-field="cargo"
  data-cell="{{ $cell?->id ?? '' }}">
  {!! $cell ? e(Str::limit($cargo, 14)) : '<span class="text-muted">+</span>' !!}
    </td>
    <td class="{{ $cell ? 'cell-has' : 'cell-empty' }}" data-date="{{ $dateKey }}" data-field="loc"
  data-cell="{{ $cell?->id ?? '' }}">
  {!! $cell ? e($loc ?? '') : '<span class="text-muted">+</span>' !!}
    </td>
    <td class="{{ $cell ? 'cell-has' : 'cell-empty' }}" data-date="{{ $dateKey }}" data-field="time"
  data-cell="{{ $cell?->id ?? '' }}">
  {!! $time ? e($time) : ($cell ? '' : '<span class="text-muted">+</span>') !!}
    </td>
  @endforeach
    </tr>
   @endforeach
  </tbody>
 </table>
</div>