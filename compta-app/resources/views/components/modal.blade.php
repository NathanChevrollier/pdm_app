@props([
    'id',
    'title' => null,
    'size' => null, // sm, lg, xl
    'staticBackdrop' => false,
    'centered' => true,
    'scrollable' => false,
    'fullscreen' => false,
    'footer' => true
])

@php
    $modalClasses = ['modal-dialog'];
    
    if ($size) {
        $modalClasses[] = 'modal-' . $size;
    }
    
    if ($centered) {
        $modalClasses[] = 'modal-dialog-centered';
    }
    
    if ($scrollable) {
        $modalClasses[] = 'modal-dialog-scrollable';
    }
    
    if ($fullscreen) {
        $modalClasses[] = 'modal-fullscreen';
    }
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" 
    @if($staticBackdrop) data-bs-backdrop="static" data-bs-keyboard="false" @endif>
    <div class="{{ implode(' ', $modalClasses) }}">
        <div class="modal-content">
            @if($title)
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if($footer)
                <div class="modal-footer">
                    @if(isset($footerContent))
                        {{ $footerContent }}
                    @else
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Fermer
                        </button>
                        <button type="button" class="btn btn-primary">Enregistrer</button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
