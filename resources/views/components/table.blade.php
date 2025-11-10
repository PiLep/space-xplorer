@props([
    'headers' => [], // Array of header definitions: ['label' => 'Name', 'key' => 'name', 'align' => 'left', 'class' => '...', 'format' => 'datetime']
    'rows' => [], // Collection or array of data
    'emptyMessage' => 'No data found',
    'emptyColspan' => null,
    'pagination' => null,
    'variant' => 'default', // default, compact, striped
    'responsive' => true,
    'hover' => true,
])

@php
    $emptyColspan = $emptyColspan ?? count($headers);
    
    $tableClasses = 'min-w-full divide-y divide-gray-200 dark:divide-gray-700';
    
    $theadClasses = 'bg-gray-50 dark:bg-gray-800';
    
    $tbodyClasses = 'bg-white dark:bg-surface-dark divide-y divide-gray-200 dark:divide-gray-700';
    if ($hover) {
        $tbodyClasses .= ' [&_tr:hover]:bg-gray-50 dark:[&_tr:hover]:bg-surface-medium';
    }
    
    $thClasses = 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider';
    
    $tdClasses = 'px-6 py-4 whitespace-nowrap text-sm';
    
    if ($variant === 'compact') {
        $thClasses = 'px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider';
        $tdClasses = 'px-4 py-2 text-sm';
    }
    
    if ($variant === 'striped') {
        $tbodyClasses .= ' [&_tr:nth-child(even)]:bg-gray-50 dark:[&_tr:nth-child(even)]:bg-surface-medium';
    }
@endphp

<div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark overflow-hidden">
    @if($responsive)
        <div class="overflow-x-auto scrollbar-hide -mr-[17px] pr-[17px]">
    @endif
    
    <table class="{{ $tableClasses }}">
        @if(!empty($headers))
            <thead class="{{ $theadClasses }}">
                <tr>
                    @foreach($headers as $header)
                        @php
                            $headerLabel = is_array($header) ? ($header['label'] ?? '') : $header;
                            $headerClass = is_array($header) ? ($header['class'] ?? '') : '';
                            $headerAlign = is_array($header) ? ($header['align'] ?? 'left') : 'left';
                            $alignClass = $headerAlign === 'right' ? 'text-right' : ($headerAlign === 'center' ? 'text-center' : 'text-left');
                        @endphp
                        <th scope="col" class="{{ $thClasses }} {{ $alignClass }} {{ $headerClass }}">
                            {{ $headerLabel }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        
        <tbody class="{{ $tbodyClasses }}">
            @if(isset($slot) && !$slot->isEmpty())
                {{ $slot }}
            @else
                @forelse($rows as $rowIndex => $row)
                    <tr>
                        @foreach($headers as $colIndex => $header)
                            @php
                                $key = is_array($header) ? ($header['key'] ?? null) : $header;
                                $cellClass = is_array($header) ? ($header['cellClass'] ?? '') : '';
                                $cellAlign = is_array($header) ? ($header['align'] ?? 'left') : 'left';
                                $alignClass = $cellAlign === 'right' ? 'text-right' : ($cellAlign === 'center' ? 'text-center' : 'text-left');
                                
                                // Get value from row
                                $value = null;
                                if ($key) {
                                    $value = data_get($row, $key);
                                }
                                
                                // Apply formatting if specified
                                $format = is_array($header) ? ($header['format'] ?? null) : null;
                                if ($format && $value) {
                                    if ($format === 'date') {
                                        $value = is_string($value) ? \Carbon\Carbon::parse($value)->format('Y-m-d') : $value->format('Y-m-d');
                                    } elseif ($format === 'datetime') {
                                        $value = is_string($value) ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : $value->format('Y-m-d H:i');
                                    } elseif ($format === 'datetime-full') {
                                        $value = is_string($value) ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') : $value->format('Y-m-d H:i:s');
                                    }
                                }
                            @endphp
                            
                            <td class="{{ $tdClasses }} {{ $alignClass }} {{ $cellClass }}">
                                {{ $value ?? '—' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $emptyColspan }}" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
    
    @if($responsive)
        </div>
    @endif
    
    @if($pagination)
        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            {{ $pagination->links() }}
        </div>
    @endif
</div>
