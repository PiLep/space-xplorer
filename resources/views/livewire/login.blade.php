<div class="max-w-md mx-auto mt-8">
    <x-form-card title="Sign In">
        <form wire:submit="login">
            <!-- Email -->
            <x-form-input
                type="email"
                name="email"
                id="email"
                label="Email"
                wireModel="email"
                placeholder="Enter your email"
                autofocus
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
                marginBottom="mb-6"
            />

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <x-button
                    type="submit"
                    variant="primary"
                    size="md"
                    wireLoading="login"
                    wireLoadingText="Signing in..."
                    class="w-full"
                >
                    Sign In
                </x-button>
            </div>

            <!-- Register Link -->
            <x-form-link
                text="Don't have an account?"
                linkText="Register"
                :href="route('register')"
            />
        </form>
    </x-form-card>
</div>
