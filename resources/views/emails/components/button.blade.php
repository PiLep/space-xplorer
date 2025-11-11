@php
    $url = $url ?? '#';
    $text = $text ?? 'Cliquez ici';
    $color = $color ?? '#00ff00';
    $textColor = $textColor ?? '#000000';
@endphp

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td align="center" style="background-color: {{ $color }}; border-radius: 0;">
                        <a href="{{ $url }}" style="display: inline-block; padding: 12px 24px; color: {{ $textColor }}; text-decoration: none; font-weight: bold; font-family: 'Courier New', monospace; font-size: 14px;">
                            {{ $text }}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

