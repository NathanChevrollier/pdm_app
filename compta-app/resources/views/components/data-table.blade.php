@props([
    'headers' => [],
    'striped' => true,
    'hover' => true,
    'bordered' => false,
    'responsive' => true,
    'small' => false,
])

@php
    $tableClasses = ['table'];
    
    if ($striped) {
        $tableClasses[] = 'table-striped';
    }
    
    if ($hover) {
        $tableClasses[] = 'table-hover';
    }
    
    if ($bordered) {
        $tableClasses[] = 'table-bordered';
    }
    
    if ($small) {
        $tableClasses[] = 'table-sm';
    }
@endphp

<div {{ $responsive ? 'class=table-responsive' : '' }}>
    <table class="{{ implode(' ', $tableClasses) }}">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
                @if(isset($actions))
                    <th class="text-center">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            {{ $slot }}
        </tbody>
    </table>
</div>
