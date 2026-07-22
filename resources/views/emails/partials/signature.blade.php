@php
    $signatureName = $name ?? 'Susan Wos';
    $signatureTitle = isset($title) ? $title : 'Founder, Salonspa Connection';
    $signatureMargin = $margin ?? '18px 0 0';
    $boldName = $boldName ?? true;
@endphp
<p style="margin:{{ $signatureMargin }};color:#111;font-size:16px;line-height:1.4;">
  Best regards,<br>
  @if ($boldName)
    <strong>{{ $signatureName }}</strong>
  @else
    {{ $signatureName }}
  @endif
  @if ($signatureTitle !== null && $signatureTitle !== '')
    <br>{{ $signatureTitle }}
  @endif
</p>
