{{-- 
    Alert Component
    Usage:
    @include('components.alert', [
        'type' => 'success',           // success, info, warning, danger
        'title' => 'Alert Title',      // Optional
        'dismissable' => true,         // Optional, allows closing the alert
        'icon' => 'fa-check'           // Optional, FontAwesome icon class
    ])
    Alert content here
    @endcomponent
--}}

<div class="alert alert-{{ $type ?? 'info' }} {{ isset($dismissable) && $dismissable ? 'alert-dismissible' : '' }}">
    @if(isset($dismissable) && $dismissable)
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    @endif
    
    @if(isset($icon))
    <h5><i class="icon fas {{ $icon }}"></i> {{ $title ?? '' }}</h5>
    @elseif(isset($title))
    <h5>{{ $title }}</h5>
    @endif
    
    {{ $slot }}
</div>
