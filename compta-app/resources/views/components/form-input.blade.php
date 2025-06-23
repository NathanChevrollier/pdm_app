@props([
    'type' => 'text',
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'error' => null
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name) || $error;
    $errorMessage = $error ?? $errors->first($name);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            class="form-control @if($hasError) is-invalid @endif"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            class="form-select @if($hasError) is-invalid @endif"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes }}
        >
            {{ $slot }}
        </select>
    @elseif($type === 'checkbox')
        <div class="form-check">
            <input
                type="checkbox"
                id="{{ $id }}"
                name="{{ $name }}"
                class="form-check-input @if($hasError) is-invalid @endif"
                value="1"
                @if(old($name, $value)) checked @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                {{ $attributes }}
            >
            <label class="form-check-label" for="{{ $id }}">
                {{ $label }}
                @if($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
        </div>
    @elseif($type === 'radio')
        <div class="form-check">
            <input
                type="radio"
                id="{{ $id }}"
                name="{{ $name }}"
                class="form-check-input @if($hasError) is-invalid @endif"
                value="{{ $value }}"
                @if(old($name) == $value) checked @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                {{ $attributes }}
            >
            <label class="form-check-label" for="{{ $id }}">
                {{ $label }}
                @if($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
        </div>
    @elseif($type === 'switch')
        <div class="form-check form-switch">
            <input
                type="checkbox"
                id="{{ $id }}"
                name="{{ $name }}"
                class="form-check-input @if($hasError) is-invalid @endif"
                value="1"
                @if(old($name, $value)) checked @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                {{ $attributes }}
            >
            <label class="form-check-label" for="{{ $id }}">
                {{ $label }}
                @if($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
        </div>
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control @if($hasError) is-invalid @endif"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes }}
        >
    @endif

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @if($hasError)
        <div class="invalid-feedback">
            {{ $errorMessage }}
        </div>
    @endif
</div>
