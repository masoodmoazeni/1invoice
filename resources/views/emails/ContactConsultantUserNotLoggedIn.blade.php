<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Consultant Inquiry Confirmation</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;">
              <p style="margin:0;color:#6b7280;font-size:14px;">Consultant Inquiry Confirmation</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:28px 32px;">
              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Your inquiry was sent to <strong>{{ $consultant_first_name }}</strong> at <strong>{{ $consultant_email }}</strong>!
              </p>

              <p style="margin:0 0 24px;color:#374151;font-size:16px;line-height:1.5;">
                Sign up to track your inquiries of favorite consultants
              </p>

              <!-- Signup Form Section -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;background:#f9fafb;border-radius:6px;padding:20px;">
                <tr>
                  <td>
                    <p style="margin:0 0 12px;color:#374151;font-size:14px;font-weight:600;">Create Your Account:</p>

                    <p style="margin:8px 0;color:#6b7280;font-size:14px;">
                      <strong>First name:</strong> {{ $first_name }}
                    </p>

                    <p style="margin:8px 0;color:#6b7280;font-size:14px;">
                      <strong>Last name:</strong> {{ $last_name }}
                    </p>

                    <p style="margin:8px 0;color:#6b7280;font-size:14px;">
                      <strong>Email Address:</strong> {{ $email }}
                    </p>

                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0 0;">
                      <tr>
                        <td align="center">
                          <a href="{{ $signup_url }}"
                             style="display:inline-block;padding:12px 22px;background:#0F4F6B;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;">
                            Create my account
                          </a>
                        </td>
                      </tr>
                    </table>

                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:12px 0 0;">
                      <tr>
                        <td align="center">
                          <a href="{{ $listing_url }}"
                             style="display:inline-block;padding:8px 16px;color:#6b7280;text-decoration:none;font-size:14px;">
                            No Thanks - Take me back to Listing
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

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

