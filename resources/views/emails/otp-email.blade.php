<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Verification Code</title>
    <style>
        /* Tamam styles jaisay verification email mein theen */
    </style>
</head>
<body>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f7f6;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="background-color: #39763a; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0;">
                            <img src="{{ asset('storage/' . \App\Models\Setting::get_logos()['logo']) }}" alt="Logo" width="150">
                            <h1 style="color: #ffffff; font-size: 28px; margin-top: 20px; margin-bottom: 0;">Your Verification Code</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #333333; font-size: 22px; margin-top: 0;">Hello {{ $user->name }},</h2>
                            <p style="font-size: 16px; color: #555555; line-height: 1.6;">Here is your One-Time Password (OTP) to complete your registration. Please enter this code on the verification page.</p>
                            <div style="text-align: center; margin: 30px 0;">
                                <p style="display: inline-block; background-color: #f0f2f5; color: #39763a; padding: 15px 30px; border-radius: 5px; font-weight: bold; font-size: 28px; letter-spacing: 5px;">{{ $otp }}</p>
                            </div>
                            <p style="font-size: 14px; color: #888888; line-height: 1.6;">This code will expire in 10 minutes. If you did not request this, you can safely ignore this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
