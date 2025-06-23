@props([
    'type' => 'button',
    'color' => 'primary',
    'size' => null,
    'outline' => false,
    'rounded' => false,
    'icon' => null,
    'iconPosition' => 'start',
    'disabled' => false,
    'block' => false,
    'href' => null
])

@php
    $tag = $href ? 'a' : 'button';
    
    $classes = ['btn'];
    
    // Button color
    if ($outline) {
        $classes[] = 'btn-outline-' . $color;
    } else {
        $classes[] = 'btn-' . $color;
    }
    
    // Button size
    if ($size) {
        $classes[] = 'btn-' . $size;
    }
    
    // Rounded
    if ($rounded) {
        $classes[] = 'rounded-pill';
    }
    
    // Block
    if ($block) {
        $classes[] = 'd-grid';
    }
    
    $attributes = $attributes->class(implode(' ', $classes));
    
    if ($href) {
        $attributes = $attributes->merge(['href' => $href]);
    } else {
        $attributes = $attributes->merge(['type' => $type]);
    }
    
    if ($disabled) {
        if ($tag === 'a') {
            $attributes = $attributes->merge(['aria-disabled' => 'true', 'tabindex' => '-1']);
            $classes[] = 'disabled';
        } else {
            $attributes = $attributes->merge(['disabled' => true]);
        }
    }
@endphp

<{{ $tag }} {{ $attributes }}>
    @if($icon && $iconPosition === 'start')
        <i class="bx {{ $icon }} me-1"></i>
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'end')
        <i class="bx {{ $icon }} ms-1"></i>
    @endif
</{{ $tag }}>
