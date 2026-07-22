<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>New Uploads have been added to Data Room {{ $listingLabel }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">
          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                Hello {{ $brokerName }},
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Financial Documents have been added to Data Room {{ $listingLabel }} by {{ $sellerName }}. Please log in to your account to review.
              </p>

              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0 28px;">
                <tr>
                  <td align="center">
                    <a href="{{ $loginUrl }}"
                       style="display:inline-block;padding:12px 22px;background:#0F4F6B;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                      Log In to Your Account
                    </a>
                  </td>
                </tr>
              </table>

              @include('emails.partials.signature')
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
