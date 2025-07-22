<option value="{{ $t->id }}" @selected(request('truck_id') == $t->id)>
 {{ $t->truck_name }}
</option>