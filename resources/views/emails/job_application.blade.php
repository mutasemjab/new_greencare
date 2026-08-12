<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب توظيف جديد</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:Tahoma, Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background:#ffffff; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="background:#33A552; padding:24px; text-align:center;">
                            <span style="color:#ffffff; font-size:20px; font-weight:bold;">طلب توظيف جديد</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#0f172a;">
                                <tr>
                                    <td style="padding:8px 0; color:#64748b; width:120px;">الوظيفة</td>
                                    <td style="padding:8px 0; font-weight:bold;">{{ $application->position }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#64748b;">الاسم</td>
                                    <td style="padding:8px 0;">{{ $application->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#64748b;">الهاتف</td>
                                    <td style="padding:8px 0;">{{ $application->phone }}</td>
                                </tr>
                                @if($application->email)
                                <tr>
                                    <td style="padding:8px 0; color:#64748b;">البريد الإلكتروني</td>
                                    <td style="padding:8px 0;">{{ $application->email }}</td>
                                </tr>
                                @endif
                                @if($application->notes)
                                <tr>
                                    <td style="padding:8px 0; color:#64748b; vertical-align:top;">ملاحظات</td>
                                    <td style="padding:8px 0;">{{ $application->notes }}</td>
                                </tr>
                                @endif
                            </table>
                            @if($application->cv)
                            <p style="margin-top:16px; font-size:13px; color:#64748b;">السيرة الذاتية مرفقة بهذه الرسالة.</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
