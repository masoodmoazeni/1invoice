<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>NDA Signature Request</title>
</head>
<body>

<p>Hello {{ $firstname }},</p>

<p>Please click the button below to sign the NDA document:</p>

<p>
    <a href="{{ $sign_url }}" 
       style="background:#5d60ed;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;">
        Sign NDA
    </a>
</p>

<p>If you did not request this, please ignore the email.</p>

<p>Thank you.</p>

</body>
</html>
