@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-8">
    <x-page-header title="Quick Review" />
    <div class="flex gap-4">
        <x-button href="{{ route('admin.resources.index') }}" variant="ghost" size="sm">
            ← Back to Resources
        </x-button>
    </div>
</div>

@livewire('admin.resource-review')
@endsection

