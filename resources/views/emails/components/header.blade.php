@php
    $prompt = $prompt ?? 'SYSTEM@STELLAR:~$';
    $message = $message ?? '';
    $type = $type ?? 'info';
@endphp

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td style="border-bottom: 1px solid #00ff00; padding-bottom: 20px; margin-bottom: 30px;">
            <div style="color: #00ff00; font-size: 14px; margin-bottom: 10px; font-family: 'Courier New', monospace;">
                {{ $prompt }}
            </div>
            @if($message)
            <div style="color: {{ $type === 'success' ? '#00ff00' : ($type === 'info' ? '#00ffff' : '#00ff00') }}; font-size: 14px; font-family: 'Courier New', monospace;">
                {{ $message }}
            </div>
            @endif
        </td>
    </tr>
</table>

