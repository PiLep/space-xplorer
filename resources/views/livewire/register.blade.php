<div class="max-w-md mx-auto mt-8">
    <x-form-card title="Create Your Account">
        <!-- General Error Message -->
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <x-alert type="error" :message="$error" :showPrompt="false" />
            @endforeach
        @endif

        <form wire:submit="register">
            <!-- Name -->
            <x-form-input
                type="text"
                name="name"
                id="name"
                label="Name"
                wireModel="name"
                placeholder="Enter your name"
                marginBottom="mb-4"
            />

            <!-- Email -->
            <x-form-input
                type="email"
                name="email"
                id="email"
                label="Email"
                wireModel="email"
                placeholder="Enter your email"
                marginBottom="mb-4"
            />

            <!-- Password -->
            <x-form-input
                type="password"
                name="password"
                id="password"
                label="Password"
                wireModel="password"
                placeholder="Enter your password"
                marginBottom="mb-4"
            />

            <!-- Password Confirmation -->
            <x-form-input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                label="Confirm Password"
                wireModel="password_confirmation"
                placeholder="Confirm your password"
                marginBottom="mb-6"
            />

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <x-button
                    type="submit"
                    variant="primary"
                    size="md"
                    wireLoading="register"
                    wireLoadingText="Registering..."
                    class="w-full"
                >
                    Register
                </x-button>
            </div>

            <!-- Login Link -->
            <x-form-link
                text="Already have an account?"
                linkText="Sign in"
                :href="route('login')"
            />
        </form>
    </x-form-card>
</div>
