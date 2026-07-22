<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>New Listing Purchase</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:30px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.06);">

          <tr>
            <td style="padding:24px;text-align:center;border-bottom:1px solid #eee;background:#fef3c7;">
              <p style="margin:0;color:#92400e;font-size:14px;font-weight:600;">New {{ $listing_type_label }} Listing Purchase</p>
            </td>
          </tr>

          <tr>
            <td style="padding:28px 32px;">
              <h1 style="margin:0 0 12px;font-size:20px;color:#0f172a;">
                A {{ $listing_type_label }} listing has been purchased
              </h1>

              <p style="margin:0 0 18px;color:#374151;font-size:16px;line-height:1.5;">
                Listing <strong>#{{ $listing_id }}, {{ $listing_title }}</strong> was just paid for on Salonspa Connection.
              </p>

              <div style="background:#f9fafb;border-radius:6px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 12px;color:#111;font-size:16px;font-weight:600;">Listing Details:</p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Listing ID:</strong> #{{ $listing_id }}
                </p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Company Name:</strong> {{ $listing_title }}
                </p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Brokers Assigned to Listing:</strong>
                  @if($assigned_brokers && count($assigned_brokers) > 0)
                    <ul style="margin:8px 0 0;padding-left:20px;">
                      @foreach($assigned_brokers as $broker)
                        <li style="margin:4px 0;">
                          {{ trim(($broker->firstname ?? '') . ' ' . ($broker->lastname ?? '')) }}
                          @if(!empty($broker->email))
                            (<a href="mailto:{{ $broker->email }}" style="color:#0F4F6B;text-decoration:none;">{{ $broker->email }}</a>)
                          @endif
                        </li>
                      @endforeach
                    </ul>
                  @else
                    None assigned
                  @endif
                </p>
              </div>

              <div style="background:#f9fafb;border-radius:6px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 12px;color:#111;font-size:16px;font-weight:600;">Payment Details:</p>

                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:12px;">
                  <thead>
                    <tr>
                      <th align="left" style="border-bottom:1px solid #e5e7eb;padding:8px 0;font-size:14px;color:#6b7280;">Item</th>
                      <th align="center" style="border-bottom:1px solid #e5e7eb;padding:8px 0;font-size:14px;color:#6b7280;">Qty</th>
                      <th align="right" style="border-bottom:1px solid #e5e7eb;padding:8px 0;font-size:14px;color:#6b7280;">Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($items as $item)
                      <tr>
                        <td style="padding:10px 0;font-size:14px;color:#111;">{{ $item['name'] }}</td>
                        <td align="center" style="padding:10px 0;font-size:14px;color:#111;">{{ $item['qty'] }}</td>
                        <td align="right" style="padding:10px 0;font-size:14px;color:#111;">${{ number_format($item['total'], 2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>

                <p style="margin:0;font-size:16px;color:#0f172a;font-weight:bold;text-align:right;">
                  Total Amount: ${{ number_format($total_amount, 2) }}
                </p>
              </div>

              <div style="background:#f9fafb;border-radius:6px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 12px;color:#111;font-size:16px;font-weight:600;">Seller Details:</p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Name:</strong> {{ $seller_name }}
                </p>

                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Email:</strong> <a href="mailto:{{ $seller_email }}" style="color:#0F4F6B;text-decoration:none;">{{ $seller_email }}</a>
                </p>

                @if($seller_phone)
                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Phone:</strong> {{ $seller_phone }}
                </p>
                @else
                <p style="margin:8px 0;color:#374151;font-size:14px;">
                  <strong>Phone:</strong> N/A
                </p>
                @endif
              </div>
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
