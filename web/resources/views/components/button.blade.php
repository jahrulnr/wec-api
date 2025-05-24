{{-- 
    Button Component
    Usage:
    @include('components.button', [
        'text' => 'Button Text',
        'type' => 'primary',           // primary, secondary, success, danger, warning, info, light, dark
        'size' => 'sm',                // sm, lg
        'outline' => true,             // Optional, outline style
        'block' => true,               // Optional, full-width button
        'disabled' => true,            // Optional, disabled state
        'icon' => 'fa-save',           // Optional, FontAwesome icon class
        'url' => '#',                  // Optional, makes the button a link
        'onclick' => 'alert("Hello")'  // Optional, adds onclick handler
    ])
--}}

@php
    $btnClass = 'btn';
    $btnClass .= ' btn-' . (isset($outline) && $outline ? 'outline-' : '') . ($type ?? 'primary');
    if (isset($size)) $btnClass .= ' btn-' . $size;
    if (isset($block) && $block) $btnClass .= ' btn-block';
@endphp

@if(isset($url))
<a href="{{ $url }}" 
   class="{{ $btnClass }}"
   {{ isset($disabled) && $disabled ? 'disabled' : '' }}
   {{ isset($onclick) ? 'onclick=' . $onclick : '' }}>
    @if(isset($icon))
    <i class="fas {{ $icon }} mr-1"></i>
    @endif
    {{ $text ?? 'Button' }}
</a>
@else
<button type="button" 
        class="{{ $btnClass }}"
        {{ isset($disabled) && $disabled ? 'disabled' : '' }}
        {{ isset($onclick) ? 'onclick=' . $onclick : '' }}>
    @if(isset($icon))
    <i class="fas {{ $icon }} mr-1"></i>
    @endif
    {{ $text ?? 'Button' }}
</button>
@endif
