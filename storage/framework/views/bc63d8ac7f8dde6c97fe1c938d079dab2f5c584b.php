<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        @import  url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body { margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Poppins', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        table { border-collapse: collapse; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #39763a; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 40px; }
        .footer { background-color: #f4f7f6; padding: 20px; text-align: center; color: #888888; font-size: 12px; }
        .button { background-color: #f8991b; color: #ffffff !important; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 16px; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Poppins', Arial, sans-serif;">
    <span style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        Just one more step to get started with JustReused!
    </span>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f7f6;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table class="container" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td class="header" style="background-color: #39763a; padding: 30px 20px; text-align: center; border-radius: 8px 8px 0 0;">
                            <img src="<?php echo e(asset('storage/' . \App\Models\Setting::get_logos()['logo'])); ?>" alt="JustReused Logo" width="150" style="display: block; margin: auto; border:0;">
                            <h1 style="color: #ffffff; font-size: 28px; margin-top: 20px; margin-bottom: 0;">Welcome to JustReused!</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td class="content" style="padding: 40px;">
                            <h2 style="color: #333333; font-size: 22px; margin-top: 0;">Almost there, <?php echo e($user->name); ?>!</h2>
                            <p style="font-size: 16px; color: #555555; line-height: 1.6;">We're excited to have you join our community. Please click the button below to confirm your email address and activate your account.</p>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo e($url); ?>" target="_blank" class="button" style="background-color: #f8991b; color: #ffffff !important; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 16px;">Verify Email Address</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size: 14px; color: #888888; line-height: 1.6;">If the button doesn't work, please copy and paste the link below into your browser:</p>
                            <p style="font-size: 12px; color: #39763a; word-break: break-all;"><a href="<?php echo e($url); ?>" style="color: #39763a;"><?php echo e($url); ?></a></p>
                            <p style="font-size: 14px; color: #888888; line-height: 1.6; margin-top: 20px;">This verification link will expire in 60 minutes.</p>
                            <p style="font-size: 16px; color: #555555; line-height: 1.6; margin-top: 30px;">Thank you,<br>The JustReused Team</p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td class="footer" style="background-color: #f4f7f6; padding: 20px; text-align: center; color: #888888; font-size: 12px;">
                            <p>You received this email because you created an account on our website. If you didn't create an account, you can safely ignore this email.</p>
                            <p>&copy; <?php echo e(date('Y')); ?> JustReused. All Rights Reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/emails/verify-email-plain.blade.php ENDPATH**/ ?>