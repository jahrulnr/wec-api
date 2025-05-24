{{-- 
    Modal Component
    Usage:
    @include('components.modal', [
        'id' => 'myModal',
        'title' => 'Modal Title',
        'size' => 'lg',                // Optional: sm, lg, xl
        'centered' => true,            // Optional: vertically center modal
        'scrollable' => true,          // Optional: adds scrollable content
        'static' => true,              // Optional: prevents closing when clicking outside
        'footerContent' => '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                           <button type="button" class="btn btn-primary">Save changes</button>'
    ])
    Modal body content here
    @endcomponent
--}}

<div class="modal fade" 
     id="{{ $id ?? 'modal' }}"
     tabindex="-1"
     role="dialog"
     aria-labelledby="{{ $id ?? 'modal' }}Label"
     aria-hidden="true"
     {{ isset($static) && $static ? 'data-backdrop="static" data-keyboard="false"' : '' }}>
    
    <div class="modal-dialog {{ isset($size) ? 'modal-' . $size : '' }} {{ isset($centered) && $centered ? 'modal-dialog-centered' : '' }} {{ isset($scrollable) && $scrollable ? 'modal-dialog-scrollable' : '' }}" role="document">
        <div class="modal-content">
            @if(isset($title))
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id ?? 'modal' }}Label">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if(isset($footerContent))
            <div class="modal-footer">
                {!! $footerContent !!}
            </div>
            @endif
        </div>
    </div>
</div>
