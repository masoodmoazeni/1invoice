<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Email Template</title>
<style>
  body {
    margin: 0;
    padding: 0;
    background: #f5f6fa;
    font-family: Arial, sans-serif;
  }
  .container {
    max-width: 600px;
    margin: 40px auto;
    background: #ffffff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
  }
  .header {
    background: #5d60ed;
    padding: 24px;
    text-align: center;
    color: white;
    font-size: 22px;
    font-weight: bold;
  }
  .content {
    padding: 30px;
    color: #333333;
    font-size: 15px;
    line-height: 1.6;
  }
  .btn {
    display: inline-block;
    padding: 12px 20px;
    background: #5d60ed;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    margin-top: 20px;
  }
  .footer {
    text-align: center;
    padding: 20px;
    color: #666;
    font-size: 13px;
  }
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      Email Notification SaloonConnection
    </div>

    <div class="content">
      <p>Hello {{ $firstname }},</p>
      <p>
        We received a request regarding your account.<br>
        Please click the button below to continue.
      </p>

      <a href="{{ $action_url }}" class="btn" style="color: #ffff;">Click Here</a>

      <p>If you did not request this, you can safely ignore this email.</p>
    </div>

    <div class="footer">
      © 2025 Saloonconnection Company. All rights reserved.
    </div>
  </div>
</body>
</html>
