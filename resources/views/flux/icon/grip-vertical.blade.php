@php
    $attributes = $unescapedForwardedAttributes ?? $attributes;
@endphp
@props([
    'variant' => 'outline',
])
@php
    $classes = \Flux\Flux::classes('shrink-0')
        ->add(match($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        });
@endphp
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none" {{ $attributes->class($classes) }} data-flux-icon aria-hidden="true">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 6C6 4.89543 6.89543 4 8 4H8.00635C9.11092 4 10.0063 4.89543 10.0063 6C10.0063 7.10457 9.11092 8 8.00635 8H8C6.89543 8 6 7.10457 6 6ZM13.9937 6C13.9937 4.89543 14.8891 4 15.9937 4H16C17.1046 4 18 4.89543 18 6C18 7.10457 17.1046 8 16 8H15.9937C14.8891 8 13.9937 7.10457 13.9937 6ZM6 12C6 10.8954 6.89543 10 8 10H8.00635C9.11092 10 10.0063 10.8954 10.0063 12C10.0063 13.1046 9.11092 14 8.00635 14H8C6.89543 14 6 13.1046 6 12ZM13.9937 12C13.9937 10.8954 14.8891 10 15.9937 10H16C17.1046 10 18 10.8954 18 12C18 13.1046 17.1046 14 16 14H15.9937C14.8891 14 13.9937 13.1046 13.9937 12ZM6 18C6 16.8954 6.89543 16 8 16H8.00635C9.11092 16 10.0063 16.8954 10.0063 18C10.0063 19.1046 9.11092 20 8.00635 20H8C6.89543 20 6 19.1046 6 18ZM13.9937 18C13.9937 16.8954 14.8891 16 15.9937 16H16C17.1046 16 18 16.8954 18 18C18 19.1046 17.1046 20 16 20H15.9937C14.8891 20 13.9937 19.1046 13.9937 18Z" fill="currentColor" />
</svg> 