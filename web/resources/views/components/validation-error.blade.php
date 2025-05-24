{{-- Custom form validation error component --}}
@props(['name'])

@error($name)
<div class="validation-error">
  <i class="fas fa-exclamation-circle"></i>
  <span>{{ $message }}</span>
</div>
@enderror
