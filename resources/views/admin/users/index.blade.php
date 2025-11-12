@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-8">
        <x-page-header title="Users" />
        <x-button href="{{ route('admin.dashboard') }}" variant="ghost" size="sm">
            ← Back to Dashboard
        </x-button>
    </div>

    <x-table
        :headers="[
            ['label' => 'ID', 'key' => 'id', 'cellClass' => 'font-mono text-gray-500 dark:text-gray-400'],
            ['label' => 'Name', 'key' => 'name', 'cellClass' => 'font-medium text-gray-900 dark:text-white'],
            ['label' => 'Email', 'key' => 'email'],
            ['label' => 'Home Planet', 'key' => 'homePlanet.name'],
            ['label' => 'Registered', 'key' => 'created_at', 'format' => 'datetime'],
            ['label' => 'Actions'],
        ]"
        :rows="$users"
        :pagination="$users"
        emptyMessage="No users found"
        hover
    >
        @foreach($users as $user)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                    {{ $user->id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                    {{ $user->name }}<span class="text-gray-400 dark:text-gray-400 ml-2">[{{ $user->matricule }}]</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->homePlanet?->name ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->created_at->format('Y-m-d H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <x-button href="{{ route('admin.users.show', $user) }}" variant="ghost" size="sm">
                        View
                    </x-button>
                </td>
            </tr>
        @endforeach
    </x-table>
@endsection

