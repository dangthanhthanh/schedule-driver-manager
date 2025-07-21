<script>
 window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

 /**
  * Serialize a form into a URL query string.
  */
 function formToQueryString(form) {
  const params = new URLSearchParams(new FormData(form));
  return '?' + params.toString();
 }

 /**
  * Load paginated JSON endpoint and rebuild a table via renderRow callback.
  * container: HTMLElement that wraps <tbody> and pagination.
  */
 async function ajaxLoadTable({ endpoint, form = null, container, renderRow }) {
  try {
   let url = endpoint;
   if (form) {
    url += formToQueryString(form);
   }
   const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
   if (!res.ok) throw new Error('HTTP ' + res.status);
   const json = await res.json(); // Laravel paginator -> {data:[],links:[],...}
   const tbody = container.querySelector('tbody');
   tbody.innerHTML = '';
   if (json.data && json.data.length) {
    json.data.forEach(item => {
     tbody.appendChild(renderRow(item));
    });
   } else {
    const tr = document.createElement('tr');
    const td = document.createElement('td');
    td.colSpan = container.querySelectorAll('thead th').length;
    td.className = 'text-center text-muted';
    td.textContent = 'Không có dữ liệu.';
    tr.appendChild(td);
    tbody.appendChild(tr);
   }
   // simple pagination replace (fallback: reload links)
   const pager = container.parentElement.querySelector('[data-ajax-pager]');
   if (pager) {
    pager.innerHTML = '';
    if (json.prev_page_url) {
     const aPrev = document.createElement('a');
     aPrev.href = json.prev_page_url;
     aPrev.textContent = '« Trang trước';
     aPrev.className = 'btn btn-sm btn-outline-secondary me-2';
     aPrev.addEventListener('click', e => {
      e.preventDefault();
      ajaxLoadTable({ endpoint: json.prev_page_url.split('?')[0], container, renderRow });
     });
     pager.appendChild(aPrev);
    }
    if (json.next_page_url) {
     const aNext = document.createElement('a');
     aNext.href = json.next_page_url;
     aNext.textContent = 'Trang sau »';
     aNext.className = 'btn btn-sm btn-outline-secondary';
     aNext.addEventListener('click', e => {
      e.preventDefault();
      ajaxLoadTable({ endpoint: json.next_page_url.split('?')[0], container, renderRow });
     });
     pager.appendChild(aNext);
    }
   }
  } catch (err) {
   console.error('ajaxLoadTable error', err);
   alert('Không tải được dữ liệu (AJAX). Vui lòng tải lại trang.');
  }
 }
</script>
 