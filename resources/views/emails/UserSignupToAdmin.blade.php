<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>New User Registration</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;background:#fef3c7;">
              <p style="margin:0;color:#92400e;font-size:14px;font-weight:600;">New User Registration</p>
            </td>
          </tr>

          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                New User Registered
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                A new user has signed up on Salonspa Connection.
              </p>

              <div style="background:#f9fafb;border-radius:6px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 12px;color:#111;font-size:16px;font-weight:600;">User Details:</p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Name:</strong> {{ $first_name }} {{ $last_name }}
                </p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Email:</strong> <a href="mailto:{{ $email }}" style="color:#0F4F6B;text-decoration:none;">{{ $email }}</a>
                </p>

                @if($mobile)
                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Mobile:</strong> {{ $mobile }}
                </p>
                @endif

                <p style="margin:12px 0 0;color:#374151;font-size:14px;">
                  <strong>User ID:</strong> {{ $user_id }}
                </p>
              </div>
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
