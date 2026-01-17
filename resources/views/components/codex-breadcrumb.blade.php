@props(['items' => []])

@if (!empty($items))
    <nav class="mb-6 font-mono" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2 text-sm">
            @foreach ($items as $index => $item)
                <li class="flex items-center">
                    @if ($index > 0)
                        <span class="mx-2 text-gray-500 dark:text-gray-500">></span>
                    @endif
                    @if (isset($item['url']) && !$loop->last)
                        <a
                            href="{{ $item['url'] }}"
                            class="text-space-secondary hover:text-space-secondary-light transition-colors"
                        >
                            [{{ strtoupper($item['label']) }}]
                        </a>
                    @else
                        <span class="text-space-primary text-glow-subtle">
                            [{{ strtoupper($item['label']) }}]
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif

