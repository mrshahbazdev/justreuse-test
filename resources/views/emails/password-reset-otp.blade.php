<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Password Reset Code</title>
</head>
<body style="font-family: 'Poppins', Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f7f6;">
        <tr><td align="center" style="padding: 20px 0;">
            <table style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px;">
                <tr><td style="background-color: #39763a; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="color: #ffffff; font-size: 28px; margin: 0;">Password Reset Request</h1>
                </td></tr>
                <tr><td style="padding: 40px;">
                    <h2 style="color: #333; font-size: 22px; margin-top: 0;">Hello {{ $user->name }},</h2>
                    <p style="font-size: 16px; color: #555; line-height: 1.6;">We received a request to reset your password. Use the code below to set up a new password for your account.</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <p style="display: inline-block; background-color: #f0f2f5; color: #39763a; padding: 15px 30px; border-radius: 5px; font-weight: bold; font-size: 28px; letter-spacing: 5px;">{{ $otp }}</p>
                    </div>
                    <p style="font-size: 14px; color: #888; line-height: 1.6;">This code will expire in 10 minutes. If you did not request a password reset, please ignore this email.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
