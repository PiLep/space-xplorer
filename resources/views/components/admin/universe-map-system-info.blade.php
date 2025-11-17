<div
    id="system-info"
    class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark mt-6 hidden rounded-lg border p-6 font-mono shadow"
>
    <h3
        id="system-name"
        class="mb-4 text-xl font-bold text-gray-900 dark:text-white"
    ></h3>
    <div class="mb-6 space-y-2 text-sm">
        <div class="flex">
            <span class="w-32 text-gray-500 dark:text-gray-400">Star Type:</span>
            <span
                id="system-star-type"
                class="text-gray-900 dark:text-white"
            ></span>
        </div>
        <div class="flex">
            <span class="w-32 text-gray-500 dark:text-gray-400">Planets:</span>
            <span
                id="system-planet-count"
                class="text-gray-900 dark:text-white"
            ></span>
        </div>
        <div class="flex">
            <span class="w-32 text-gray-500 dark:text-gray-400">Coordinates:</span>
            <span
                id="system-coordinates"
                class="text-gray-900 dark:text-white"
            ></span>
        </div>
        <div class="flex">
            <span class="w-32 text-gray-500 dark:text-gray-400">Status:</span>
            <span
                id="system-status"
                class="text-gray-900 dark:text-white"
            ></span>
        </div>
    </div>

    <!-- Nearby Systems -->
    <div
        id="nearby-systems-section"
        class="hidden"
    >
        <h4
            class="mb-3 border-b border-gray-300 pb-2 text-lg font-semibold text-gray-900 dark:border-gray-700 dark:text-white">
            Nearby Systems
        </h4>
        <div
            id="nearby-systems-list"
            class="space-y-2 text-sm"
        >
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

