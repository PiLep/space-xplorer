<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Profile Settings"
        description="Manage your account information and preferences."
    />

    @if ($loading)
        <x-loading-spinner variant="simple" size="md" :showMessage="false" />
    @elseif ($user)
        <x-form-card title="Account Information" headerSeparated shadow="shadow-lg" padding="px-8 py-6">
            <form wire:submit="updateProfile">
                <!-- Success Message -->
                @if ($success)
                    <x-alert type="success" :message="$success" :showPrompt="false" />
                @endif

                <!-- Error Message -->
                @if ($error)
                    <x-alert type="error" :message="$error" :showPrompt="false" />
                @endif

                <!-- Name -->
                <x-form-input
                    type="text"
                    name="name"
                    id="name"
                    label="Name"
                    wireModel="name"
                    placeholder="Enter your name"
                    marginBottom="mb-6"
                />

                <!-- Email -->
                <x-form-input
                    type="email"
                    name="email"
                    id="email"
                    label="Email"
                    wireModel="email"
                    placeholder="Enter your email"
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

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <x-button
                        href="{{ route('dashboard') }}"
                        variant="ghost"
                        size="md"
                    >
                        Cancel
                    </x-button>
                    <x-button
                        type="submit"
                        variant="primary"
                        size="md"
                        wireLoading="updateProfile"
                        wireLoadingText="Saving..."
                    >
                        Save Changes
                    </x-button>
                </div>
            </form>
        </x-form-card>
    @endif
</div>
