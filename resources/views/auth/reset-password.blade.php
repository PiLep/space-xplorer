@php
    // This view is used by the controller to pass token and email to Livewire component
@endphp

<livewire:reset-password :token="$token" :email="$email" />

