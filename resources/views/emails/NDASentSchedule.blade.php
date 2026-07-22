<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>NDA Sent</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">
          <!-- Body -->
          <tr>
            <td style="padding:28px 32px;">
              
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                Confidentiality Agreement Required to learn more about ({{ $listing_title }})
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Hello <strong>{{ $first_name }}</strong>,
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Thank you for your interest in Listing #<strong>{{ $listing_id }}</strong>, 
                <em>{{ $listing_title }}</em>.  
                This is a private sale, which means a confidentiality agreement (NDA) is required to learn more about the business.
              </p>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                This protects the business's identity as well as your personal interest.  
                Once we receive your signed NDA, you will receive more information about the business.
              </p>

              <p style="margin:0 0 6px;color:#374151;font-size:16px;line-height:1.5;">
                If you have any questions or need further assistance, please don't hesitate to reach out.
              </p>

              <p style="margin:0 0 6px;color:#374151;font-size:16px;line-height:1.5;">
                <a href="{{ $signUrl }}">Please Sign NDA Here</a>
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
