<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Consultant inquiry confirmation</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;">
              <p style="margin:0;color:#6b7280;font-size:14px;">Consultant inquiry confirmation</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                Hi there, {{ $first_name }},
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                This email is confirmation that <strong>{{ $consultant_full_name }}</strong> was sent your interest in speaking with them about their services. You can track your favorite consultants and coaches by logging into your account at
                <a href="{{ $login_page_url }}" style="color:#0F4F6B;text-decoration:none;">{{ $login_page_url }}</a>. If you do not have an account, you can create one by using this email address.
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Looking to expand? Buying an existing business is the fast track to wealth! Check out our Beauty Businesses for Sale, here:
                <a href="{{ $beauty_businesses_url }}" style="color:#0F4F6B;text-decoration:none;">Beauty Industry Businesses for Sale</a>
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Thinking about selling your Beauty Business? Chat with one of our brokers, today:
                <a href="{{ $broker_chat_url }}" style="color:#0F4F6B;text-decoration:none;">Chat with A Beauty Business Broker</a>
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                If you have any questions or need further assistance, please don't hesitate to reach out.
              </p>

              @include('emails.partials.signature')

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:12px 20px;background:#f9fafb;border-top:1px solid #eee;text-align:center;color:#9ca3af;font-size:13px;">
              <small>© 2026 Salonspa Connection. All rights reserved.</small>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
