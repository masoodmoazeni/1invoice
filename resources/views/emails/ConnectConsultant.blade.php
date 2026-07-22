<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Consultant Notification</title>
</head>

<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0"
          style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;">
              <p style="margin:0;color:#6b7280;font-size:14px;">Consultant Notification</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                Hi {{ $first_name }},
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                This email confirms that <strong>{{ $consultant_name }}</strong> was sent your interest in speaking with
                them about their services.
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                You can track your favorite consultants and coaches by logging into your account:
              </p>

              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:14px 0 22px;">
                <tr>
                  <td align="center">
                    <a href={{ config('app.frontend_url') . '/login' }}
                      style="display:inline-block;padding:12px 22px;background:#0F4F6B;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                      style="display:inline-block;padding:12px
                      22px;background:#0F4F6B;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                      Log In to Your Account
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Looking to expand? Buying an existing business is the fast track to wealth!
                <a href={{ config('app.frontend_url') . '/beauty-businesses-for-sale' }}
                  style="color:#0F4F6B;text-decoration:none;">Beauty Industry Businesses for Sale</a>
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Thinking about selling your Beauty Business? Chat with one of our brokers today:
                <a href={{ config('app.frontend_url') . '/chat-with-a-business-broker' }}
                  style="color:#0F4F6B;text-decoration:none;">Chat with A Beauty Business Broker</a>
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                If you have any questions or need further assistance, please don't hesitate to reach out.
              </p>

              @include('emails.partials.signature')
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td
              style="padding:12px 20px;background:#f9fafb;border-top:1px solid #eee;text-align:center;color:#9ca3af;font-size:13px;">
              <small>© 2026 Salonspa Connection. All rights reserved.</small>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>

</html>