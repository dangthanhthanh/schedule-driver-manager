@if(session('success'))
 <div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
 </div>
@endif

@if($errors->any())
 <div class="alert alert-danger">
  <strong>Lỗi:</strong>
  <ul class="mb-0">
   @foreach($errors->all() as $err)
    <li>{{ $err }}</li>
   @endforeach
  </ul>
 </div>
@endif