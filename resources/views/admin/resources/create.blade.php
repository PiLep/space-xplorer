@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-8">
    <x-page-header title="Generate Resource" />
    <x-button href="{{ route('admin.resources.index') }}" variant="ghost" size="sm">
        ← Back to Resources
    </x-button>
</div>

<div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark">
    <div class="px-4 py-5 sm:p-6">
        @livewire('admin.resource-form')
    </div>
</div>
@endsection

