<tr>
 <td>{{ $d->id }}</td>
 <td>{{ $d->name }}</td>
 <td>{{ $d->phone }}</td>
 <td>{{ $d->truck?->truck_name ?? '—' }}</td>
 <td>
  {!! $d->is_main_driver ? '<span class="badge bg-success">Chính</span>' : '<span class="badge bg-secondary">Phụ</span>' !!}
 </td>
 <td>
  <a href="{{ route('drivers.status-log', $d) }}" class="btn btn-sm btn-outline-info">Log</a>
 </td>
 <td class="text-end">
  <a href="{{ route('drivers.edit', $d) }}" class="btn btn-sm btn-outline-warning">Sửa</a>
  <form action="{{ route('drivers.destroy', $d) }}" method="POST" class="d-inline"
   onsubmit="return confirm('Xóa tài xế?')">
   @csrf @method('DELETE')
   <button class="btn btn-sm btn-outline-danger submit-once">Xóa</button>
  </form>
 </td>
</tr>