<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Activate your listing</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;">
              <p style="margin:0;color:#6b7280;font-size:14px;">Salonspa Connection</p>
            </td>
          </tr>

          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                Hello {{ $first_name }},
              </h1>

              <p style="margin:0 0 16px;color:#374151;font-size:16px;line-height:1.5;">
                Your Broker, <strong>{{ $broker_name ?: 'your broker' }}</strong>, has created a listing for your business!
                Please review the details and then activate your listing by using the payment link below
                – or through your <a href="{{ $dashboard_link }}" style="color:#0F4F6B;">Dashboard</a>.
              </p>

              <p style="margin:0 0 8px;color:#374151;font-size:15px;line-height:1.5;">
                <strong>Listing #{{ $listing_id }}</strong> — {{ $listing_title }}
              </p>

              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:16px 0 20px;">
                <tr>
                  <td align="center" style="padding:0 0 12px;">
                    <a href="{{ $listing_link }}"
                      style="display:inline-block;padding:10px 20px;background:#ffffff;color:#0F4F6B;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;border:2px solid #0F4F6B;">
                      View listing details
                    </a>
                  </td>
                </tr>
                <tr>
                  <td align="center">
                    <a href="{{ $payment_link }}"
                      style="display:inline-block;padding:10px 20px;background:#0F4F6B;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                      Complete payment
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Once payment is completed, your listing will be live on our website and marketing will begin!
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                If you have any questions or need further assistance, please don't hesitate to reach out.
              </p>

              @include('emails.partials.signature')
            </td>
          </tr>

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
