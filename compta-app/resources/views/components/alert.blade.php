@props(['type' => 'info', 'message', 'dismissible' => true])

@php
    $alertClass = match($type) {
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        'primary' => 'alert-primary',
        'secondary' => 'alert-secondary',
        default => 'alert-info'
    };

    $icon = match($type) {
        'success' => 'bx bx-check-circle',
        'danger' => 'bx bx-error-circle',
        'warning' => 'bx bx-error',
        'info' => 'bx bx-info-circle',
        'primary' => 'bx bx-bell',
        'secondary' => 'bx bx-badge',
        default => 'bx bx-info-circle'
    };
@endphp

<div {{ $attributes->merge(['class' => 'alert ' . $alertClass . ($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
    <i class="{{ $icon }} me-2"></i>
    {{ $message }}
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
