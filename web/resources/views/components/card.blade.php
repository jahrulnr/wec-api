{{-- 
    Card Component
    Usage:
    @include('components.card', [
        'title' => 'Card Title',
        'tools' => true,                 // Optional, enables the card tools
        'collapsable' => true,           // Optional, enables the collapse button
        'removable' => true,             // Optional, enables the remove button
        'color' => 'primary',            // Optional, card color (primary, info, success, warning, danger)
        'outline' => true,               // Optional, card outline style
        'bodyClass' => 'p-0',            // Optional, additional classes for card body
        'footerContent' => 'Card Footer' // Optional, footer content
    ])
    Card Body Content Here
    @endcomponent
--}}

<div class="card 
    {{ isset($color) ? 'card-' . $color : '' }} 
    {{ isset($outline) && $outline ? 'card-outline' : '' }}">
    
    @if(isset($title))
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>

        @if(isset($tools) && $tools)
        <div class="card-tools">
            @if(isset($collapsable) && $collapsable)
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            @endif
            @if(isset($removable) && $removable)
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
            @endif
        </div>
        @endif
    </div>
    @endif
    
    <div class="card-body {{ isset($bodyClass) ? $bodyClass : '' }}">
        {{ $slot }}
    </div>
    
    @if(isset($footerContent))
    <div class="card-footer">
        {{ $footerContent }}
    </div>
    @endif
</div>
