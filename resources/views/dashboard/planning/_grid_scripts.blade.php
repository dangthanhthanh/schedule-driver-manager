<script>
 (function () {
  const mode = {{ json_encode($mode) }};
  const isTransport = {{ isset($transport) && $transport ? 'true' : 'false' }};
  const saveUrl = planningCell.saveUrl;
  const delUrl = planningCell.delUrl;

  const modalEl = document.getElementById('planningCellModal');
  const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
  const formEl = document.getElementById('planningCellForm');

  // open when click a cell
  document.addEventListener('click', function (e) {
   const cell = e.target.closest('td[data-date]');
   if (!cell) return;
   const row = cell.parentElement;
   const date = cell.dataset.date;
   const cellId = cell.dataset.cell;

   // set hidden fields
   document.getElementById('cell_date').value = date;
   document.getElementById('cell_id').value = cellId || '';

   if (mode === 'driver') {
    const driverId = row.dataset.driver;
    document.getElementById('cell_driver').value = driverId;
    // if select of truck exists (transport or driver mode)
    const selTruck = document.getElementById('cell_truck_sel');
    if (selTruck) selTruck.value = '';
   } else {
    const truckId = row.dataset.truck;
    document.getElementById('cell_truck').value = truckId;
    const selDriver = document.getElementById('cell_driver_sel');
    if (selDriver) selDriver.value = '';
   }

   // reset form fields
   formEl.reset();
   // re-apply hidden values
   document.getElementById('cell_date').value = date;
   document.getElementById('cell_id').value = cellId || '';
   if (mode === 'driver') { document.getElementById('cell_driver').value = row.dataset.driver; }
   else { document.getElementById('cell_truck').value = row.dataset.truck; }

   // If editing existing -> fetch details?
   if (cellId) {
    fetch('{{ url('/api/schedule-truck') }}/' + cellId, { headers: { 'Accept': 'application/json' } })
     .then(r => r.json())
     .then(j => {
      if (j.id) {
       if (mode === 'driver' && document.getElementById('cell_truck_sel')) document.getElementById('cell_truck_sel').value = j.truck_id ?? '';
       if (mode === 'truck' && document.getElementById('cell_driver_sel')) document.getElementById('cell_driver_sel').value = j.driver_id ?? '';
       document.getElementById('cell_location').value = j.to_location_id ?? '';
       document.getElementById('cell_status').value = j.status ?? '';
       document.getElementById('cell_pickup_time').value = j.pickup_time ?? '';
       document.getElementById('cell_assistant').value = j.assistant ?? '';
       document.getElementById('cell_cargo').value = j.cargo_desc ?? '';
      }
     }).catch(() => { });
    document.getElementById('cell_delete_btn').style.display = '';
   } else {
    document.getElementById('cell_delete_btn').style.display = 'none';
   }

   modal?.show();
  });

  // Save
  formEl?.addEventListener('submit', function (e) {
   e.preventDefault();
   const fd = new FormData(formEl);
   fetch(saveUrl, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': window.csrfToken },
    body: fd
   })
    .then(r => r.json())
    .then(j => {
     if (j.cell) {
      // reload grid quick
      reloadGrid();
      modal?.hide();
     } else {
      alert(j.message || 'Lỗi lưu.');
     }
    })
    .catch(err => {
     console.error(err);
     alert('Không lưu được.');
    });
  });

  // Delete
  document.getElementById('cell_delete_btn')?.addEventListener('click', function () {
   if (!confirm('Xoá lịch này?')) return;
   const fd = new FormData();
   fd.append('schedule_truck_id', document.getElementById('cell_id').value);
   fetch(delUrl, {
    method: 'POST', // Laravel hỗ trợ _method=DELETE
    headers: { 'X-CSRF-TOKEN': window.csrfToken },
    body: (() => {
     fd.append('_method', 'DELETE');
     return fd;
    })()
   })
    .then(r => r.json())
    .then(j => {
     reloadGrid();
     modal?.hide();
    })
    .catch(() => alert('Không xoá được.'));
  });

  // Filter reload
  const filterFormId = mode === 'truck' ? 'trucks-grid-filter' : (isTransport ? 'transport-grid-filter' : 'drivers-grid-filter');
  const filterForm = document.getElementById(filterFormId);
  filterForm?.addEventListener('submit', function (e) {
   e.preventDefault();
   reloadGrid();
  });

  function reloadGrid() {
   if (!filterForm) return;
   const url = filterForm.dataset.endpoint + '?' + new URLSearchParams(new FormData(filterForm));
   fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(j => {
     const wrapperId = mode === 'truck' ? 'trucks-grid-wrapper' : (isTransport ? 'transport-grid-wrapper' : 'drivers-grid-wrapper');
     const wrap = document.getElementById(wrapperId);
     if (wrap) wrap.innerHTML = j.html;
    });
  }
 })();
</script>