<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>New Consultant Inquiry - Admin Notification</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;background:#fef3c7;">
              <p style="margin:0;color:#92400e;font-size:14px;font-weight:600;">Admin Notification - New Consultant Inquiry</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                New Consultant Inquiry Received
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                A new inquiry has been submitted through the consultant contact form.
              </p>

              <div style="background:#f9fafb;border-radius:6px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 12px;color:#111;font-size:16px;font-weight:600;">Inquiry Details:</p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Contact Name:</strong> {{ $first_name }} {{ $last_name }}
                </p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Email:</strong> <a href="mailto:{{ $email }}" style="color:#0F4F6B;text-decoration:none;">{{ $email }}</a>
                </p>

                @if($phone)
                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Phone:</strong> {{ $phone }}
                </p>
                @endif

                <p style="margin:12px 0 8px;color:#111;font-size:16px;font-weight:600;">Consultant Information:</p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Consultant:</strong> {{ $consultant_first_name }} {{ $consultant_last_name }}
                </p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Consultant Email:</strong> <a href="mailto:{{ $consultant_email }}" style="color:#0F4F6B;text-decoration:none;">{{ $consultant_email }}</a>
                </p>

                @if($connection)
                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>How did they hear about us:</strong> {{ $connection }}
                </p>
                @endif

                @if($describe_service)
                <p style="margin:12px 0 0;color:#374151;font-size:14px;">
                  <strong>Service Description:</strong>
                </p>
                <p style="margin:4px 0 0;color:#6b7280;font-size:14px;line-height:1.5;">
                  {{ $describe_service }}
                </p>
                @endif

                @if($user_id)
                <p style="margin:12px 0 0;color:#374151;font-size:14px;">
                  <strong>User ID:</strong> {{ $user_id }} (Logged in user)
                </p>
                @else
                <p style="margin:12px 0 0;color:#6b7280;font-size:14px;">
                  <strong>User Status:</strong> Not logged in
                </p>
                @endif
              </div>

              @include('emails.partials.signature', ['name' => 'SalonSpa Connection System', 'title' => ''])

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

