<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Thank You for Your Inquiry</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;">
              <p style="margin:0;color:#6b7280;font-size:14px;">Inquiry Received</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:28px 32px;">

              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                Thank you for your interest in {{ $listing_title }}!
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Thank you, <strong>{{ $first_name }}</strong>, for your inquiry!
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                We have received your interest in Listing #<strong>{{ $listing_id }}</strong>,
                <em>{{ $listing_title }}</em>.
                Your inquiry has been forwarded to the representative of this business.
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                If a confidentiality agreement (NDA) is required for this listing, you will receive an additional email soon with instructions to sign it.
                </p>
                <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                  If this listing is not the right fit, or you are actively looking for additional beauty businesses to acquire, please add yourself to our private Buyer's List to receive new opportunities before they go to market.
                </p>
            <!-- Buyer's List Button -->
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0 28px;">
                <tr>
                <td align="center">
                    <a href="{{ $buyer_form_url ?? '#' }}"
                        style="display:inline-block;padding:12px 22px;background:#0F4F6B;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                    Join Buyer’s List
                    </a>
                </td>
                </tr>
            </table>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                The representative for this business will be in touch shortly!
              </p>

              <p style="margin:0;color:#6b7280;font-size:14px;line-height:1.5;">
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
