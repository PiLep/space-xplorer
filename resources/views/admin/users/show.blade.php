@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-8">
        <x-page-header title="User Details" />
        <div class="flex gap-4">
            <x-button href="{{ route('admin.users.index') }}" variant="ghost" size="sm">
                ← Back to Users
            </x-button>
            <x-button href="{{ route('admin.dashboard') }}" variant="ghost" size="sm">
                Dashboard
            </x-button>
        </div>
    </div>

    <div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $user->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->name }}<span class="text-gray-400 dark:text-gray-400 ml-2">[{{ $user->matricule }}]</span></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registered</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('Y-m-d H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->updated_at->format('Y-m-d H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verified</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'Not verified' }}
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Home Planet</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        @if($user->homePlanet)
                            <div class="mt-2">
                                <p class="font-medium">{{ $user->homePlanet->name }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ $user->homePlanet->description }}</p>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span>Type: {{ $user->homePlanet->type }}</span> |
                                    <span>Size: {{ $user->homePlanet->size }}</span> |
                                    <span>Temperature: {{ $user->homePlanet->temperature }}</span>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-500 dark:text-gray-400">No home planet assigned</span>
                        @endif
                    </dd>
                </div>
                @if($user->hasAvatar())
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Avatar</dt>
                        <dd class="mt-1">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }} [{{ $user->matricule }}]" class="h-24 w-24 rounded-full object-cover">
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
        <div class="px-4 py-4 sm:px-6 border-t border-border-dark dark:border-border-dark flex justify-end gap-4">
            <livewire:admin.user-delete-button :user="$user" />
        </div>
    </div>
@endsection

