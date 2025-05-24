{{-- Alert untuk menampilkan error/success notification --}}
@if (session('status'))
<div class="alert-box success">
  <div class="alert-icon">
    <i class="fas fa-check-circle"></i>
  </div>
  <div class="alert-content">
    {{ session('status') }}
  </div>
</div>
@endif

@if ($errors->any())
<div class="alert-box error">
  <div class="alert-icon">
    <i class="fas fa-exclamation-circle"></i>
  </div>
  <div class="alert-content">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
</div>
@endif
