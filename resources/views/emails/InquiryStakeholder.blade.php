<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>New buyer inquiry</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;">
              <p style="margin:0;color:#6b7280;font-size:14px;">New buyer inquiry</p>
            </td>
          </tr>

          <tr>
            <td style="padding:28px 32px;">

              <h1 style="margin:0 0 16px;font-size:20px;color:#0f172a;">
                {{ $buyer_name }} has inquired about Listing #{{ $listing_id }}, {{ $listing_title }}
              </h1>

              <p style="margin:0 0 20px;color:#374151;font-size:16px;line-height:1.5;">
                <strong>Buyer contact</strong><br>
                Email: {{ $buyer_email ?? '—' }}<br>
                Phone: {{ $buyer_phone ?? '—' }}
              </p>

              <h2 style="margin:0 0 12px;font-size:16px;color:#0f172a;">Buyer inquiry — questions and answers</h2>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:6px;overflow:hidden;">
                @foreach ($qa_rows as $row)
                  <tr>
                    <td style="padding:12px 14px;vertical-align:top;width:42%;background:#f9fafb;color:#374151;font-size:14px;line-height:1.45;border-bottom:1px solid #e5e7eb;">
                      {{ $row['question'] }}
                    </td>
                    <td style="padding:12px 14px;vertical-align:top;color:#111;font-size:14px;line-height:1.45;border-bottom:1px solid #e5e7eb;">
                      {!! nl2br(e($row['answer'])) !!}
                    </td>
                  </tr>
                @endforeach
              </table>

              <p style="margin:24px 0 0;color:#374151;font-size:16px;line-height:1.5;">
                <strong>Is an NDA required for this Listing?</strong> {{ $nda_required ? 'YES' : 'NO' }}<br>
                <strong>Listing Type:</strong> {{ $listing_type_label }}
              </p>

              <p style="margin:24px 0 0;color:#6b7280;font-size:13px;line-height:1.5;">
                This message was sent because a buyer submitted an inquiry on Salonspa Connection.
              </p>

            </td>
          </tr>

          <tr>
            <td style="padding:12px 20px;background:#f9fafb;border-top:1px solid #eee;text-align:center;color:#9ca3af;font-size:13px;">
              <small>© {{ date('Y') }} Salonspa Connection. All rights reserved.</small>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
