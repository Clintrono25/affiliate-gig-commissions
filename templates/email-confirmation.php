<?php
// Variables expected: $confirm_link, $user_id
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirm Your Email</title>
</head>
<body style="font-family:sans-serif;background:#f7f7f7;padding:30px;">
    <div style="max-width:480px;margin:0 auto;background:#fff;padding:32px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.04)">
        <h2 style="color:#2E7D32;">Welcome to Our Affiliate Program</h2>
        <p>Thank you for registering!</p>
        <p>Please confirm your email by clicking the button below:</p>
        <p style="text-align:center;margin:32px 0;">
            <a href="<?php echo esc_url($confirm_link); ?>" style="display:inline-block;padding:12px 32px;background:#2E7D32;color:#fff;text-decoration:none;border-radius:5px;font-size:18px;">Confirm Email</a>
        </p>
        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p style="word-break:break-all;"><?php echo esc_html($confirm_link); ?></p>
        <hr style="border:none;border-top:1px solid #eee;">
        <p style="font-size:12px;color:#888;">If you did not register for our affiliate program, please ignore this email.</p>
    </div>
</body>
</html>