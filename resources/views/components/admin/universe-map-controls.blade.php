@props([
    'systemCount' => 0,
])

<div class="mb-6 flex flex-wrap items-center gap-4 font-mono">
    <div class="flex items-center gap-2">
        <span class="text-sm uppercase text-gray-500 dark:text-gray-400">View Plane:</span>
        <button
            id="view-xy"
            onclick="changeViewPlane('xy')"
            class="rounded bg-blue-600 px-3 py-1 text-sm text-white transition"
        >
            XY
        </button>
        <button
            id="view-xz"
            onclick="changeViewPlane('xz')"
            class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
        >
            XZ
        </button>
        <button
            id="view-yz"
            onclick="changeViewPlane('yz')"
            class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
        >
            YZ
        </button>
    </div>

    <div class="flex items-center gap-2">
        <button
            onclick="zoomIn()"
            class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
        >
            Zoom +
        </button>
        <span
            id="zoom-level"
            class="text-sm text-gray-500 dark:text-gray-400"
        >1.00x</span>
        <button
            onclick="zoomOut()"
            class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
        >
            Zoom -
        </button>
        <button
            onclick="resetView()"
            class="rounded bg-blue-600 px-3 py-1 text-sm text-white transition hover:bg-blue-700"
            title="Reset view to fit all systems"
        >
            Reset View
        </button>
    </div>

    <div class="flex items-center gap-2">
        <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <input
                type="checkbox"
                id="show-connections"
                onchange="toggleConnections()"
                class="rounded"
            >
            <span>Show Connections</span>
        </label>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <label for="max-distance">Max Distance:</label>
            <input
                type="range"
                id="max-distance"
                min="50"
                max="500"
                value="200"
                step="50"
                oninput="updateMaxDistance(this.value)"
                class="w-24"
            >
            <span
                id="max-distance-value"
                class="w-16 text-left"
            >200 AU</span>
        </div>
    </div>

    <div class="ml-auto text-sm text-gray-500 dark:text-gray-400">
        Systems: {{ $systemCount }}
    </div>
</div>

