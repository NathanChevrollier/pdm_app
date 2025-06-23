@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
    'hasFooter' => false
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || isset($header))
        <div class="card-header {{ $headerClass }}">
            @if(isset($header))
                {{ $header }}
            @else
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $title }}</h5>
                    @if($subtitle)
                        <small class="text-muted">{{ $subtitle }}</small>
                    @endif
                    @if(isset($headerActions))
                        <div class="card-header-actions">
                            {{ $headerActions }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
    
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
    
    @if($hasFooter || isset($footer))
        <div class="card-footer {{ $footerClass }}">
            @if(isset($footer))
                {{ $footer }}
            @endif
        </div>
    @endif
</div>
