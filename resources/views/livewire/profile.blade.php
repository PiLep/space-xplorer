<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Profile Settings"
        description="Manage your account information and preferences."
    />

    @if ($loading)
        <x-loading-spinner variant="simple" size="md" :showMessage="false" />
    @elseif ($user)
        <x-form-card title="Account Information" headerSeparated shadow="shadow-lg" padding="px-8 py-6">
            <!-- Error Message -->
            @if ($error)
                <x-alert type="error" :message="$error" :showPrompt="false" />
            @endif

            <!-- Name (read-only) -->
            <x-form-input
                type="text"
                name="name"
                id="name"
                label="Name"
                value="{{ $user['name'] }}"
                disabled
                marginBottom="mb-6"
            />

            <!-- Email (read-only) -->
            <x-form-input
                type="email"
                name="email"
                id="email"
                label="Email"
                value="{{ $user['email'] }}"
                disabled
                marginBottom="mb-6"
            />

            <!-- User ID (read-only) -->
            <x-form-input
                type="text"
                name="user_id"
                id="user_id"
                label="User ID"
                value="{{ $user['id'] }}"
                disabled
                helpText="This is your unique user identifier."
                marginBottom="mb-6"
            />

            <!-- Home Planet ID (read-only) -->
            @if ($user['home_planet_id'])
                <x-form-input
                    type="text"
                    name="home_planet_id"
                    id="home_planet_id"
                    label="Home Planet ID"
                    value="{{ $user['home_planet_id'] }}"
                    disabled
                    helpText="Your home planet identifier."
                    marginBottom="mb-6"
                />
            @endif

            <!-- Back Button -->
            <div class="flex items-center justify-end">
                <x-button
                    href="{{ route('dashboard') }}"
                    variant="primary"
                    size="md"
                >
                    Back to Dashboard
                </x-button>
            </div>
        </x-form-card>
    @else
        <!-- Error state: user data not loaded -->
        <x-form-card title="Account Information" headerSeparated shadow="shadow-lg" padding="px-8 py-6">
            @if ($error)
                <x-alert type="error" :message="$error" :showPrompt="false" />
            @else
                <x-alert type="error" message="Failed to load user data. Please try refreshing the page." :showPrompt="false" />
            @endif

            <div class="mt-6">
                <x-button
                    wire:click="loadUser"
                    variant="primary"
                    size="md"
                >
                    Retry
                </x-button>
                <x-button
                    href="{{ route('dashboard') }}"
                    variant="ghost"
                    size="md"
                    class="ml-4"
                >
                    Back to Dashboard
                </x-button>
            </div>
        </x-form-card>
    @endif
</div>
