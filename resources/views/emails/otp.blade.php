<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رمز التحقق</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:Tahoma, Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:420px; background:#ffffff; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="background:#33A552; padding:24px; text-align:center;">
                            <span style="color:#ffffff; font-size:20px; font-weight:bold;">Green Care</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 24px; text-align:center;">
                            <p style="color:#0f172a; font-size:16px; margin:0 0 16px;">رمز التحقق الخاص بك هو:</p>
                            <div style="font-size:32px; font-weight:bold; letter-spacing:8px; color:#33A552; margin:0 0 16px;">
                                {{ $otp }}
                            </div>
                            <p style="color:#64748b; font-size:13px; margin:0;">
                                الرمز صالح لمدة {{ $expiryMinutes }} دقائق. إذا لم تطلب هذا الرمز تجاهل هذه الرسالة.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
