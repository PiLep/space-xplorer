@php
    $appName = $appName ?? 'Stellar';
    $appDescription = $appDescription ?? 'Exploration Spatiale Interactive';
@endphp

<table
    role="presentation"
    cellspacing="0"
    cellpadding="0"
    border="0"
    width="100%"
    style="margin-top: 30px;"
>
    <tr>
        <td style="border-top: 1px solid #00ff00; padding-top: 20px;">
            <div style="color: #00ff00; font-size: 14px; margin-bottom: 10px; font-family: 'Courier New', monospace;">
                SYSTEM@STELLAR:~$
            </div>
            <div style="font-size: 12px; color: #666666; font-family: 'Courier New', monospace;">
                {{ $appName }} - {{ $appDescription }}
            </div>
        </td>
    </tr>
</table>
