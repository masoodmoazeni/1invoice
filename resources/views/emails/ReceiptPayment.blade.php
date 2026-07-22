<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Payment Receipt</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;">
              <p style="margin:0;color:#6b7280;font-size:14px;">Salonspa Connection Receipt</p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                Hello {{ $first_name }},
              </h1>

              <p style="margin:0 0 16px;color:#374151;font-size:16px;line-height:1.5;">
                This email is your receipt for purchases from Salonspa Connection Brokerage.<br>
                Email for: {{ $seller_email }}<br>
                Assigned Broker: {{ $broker_email ?? 'N/A' }}<br><br>
                Thank you for purchasing the following service(s):
              </p>

              <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;">
                <thead>
                  <tr>
                    <th align="left" style="border-bottom:1px solid #e5e7eb;padding:8px 0;font-size:14px;color:#6b7280;">
                      Item
                    </th>
                    <th align="center" style="border-bottom:1px solid #e5e7eb;padding:8px 0;font-size:14px;color:#6b7280;">
                      Qty
                    </th>
                    <th align="right" style="border-bottom:1px solid #e5e7eb;padding:8px 0;font-size:14px;color:#6b7280;">
                      Price
                    </th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($items as $item)
                    <tr>
                      <td style="padding:10px 0;font-size:15px;color:#111;">
                        {{ $item['name'] }}
                      </td>
                      <td align="center" style="padding:10px 0;font-size:15px;color:#111;">
                        {{ $item['qty'] }}
                      </td>
                      <td align="right" style="padding:10px 0;font-size:15px;color:#111;">
                        ${{ number_format($item['total'], 2) }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>

              <hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">

              <p style="margin:0;font-size:17px;color:#0f172a;font-weight:bold;text-align:right;">
                Total Amount: ${{ number_format($total_amount, 2) }}
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
