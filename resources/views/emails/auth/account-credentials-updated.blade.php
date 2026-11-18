<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your TrabaGo login details were updated</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f3;color:#17221e;font-family:Arial,Helvetica,sans-serif;">
    <span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;overflow:hidden;">
        Your TrabaGo login details have been updated.
    </span>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f1f5f3;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background-color:#ffffff;border:1px solid #dce5e0;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px 32px;background-color:#064e3b;color:#ffffff;">
                            <p style="margin:0;font-size:20px;line-height:28px;font-weight:700;">TrabaGo</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;font-size:13px;line-height:20px;font-weight:700;color:#047857;text-transform:uppercase;">Account security</p>
                            <h1 style="margin:0 0 16px;font-size:24px;line-height:32px;color:#0f172a;">Your login details were updated</h1>
                            <p style="margin:0 0 20px;font-size:15px;line-height:24px;color:#475569;">
                                An authorized TrabaGo staff member updated the login details for your account.
                            </p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 16px;background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0 0 6px;font-size:11px;line-height:16px;font-weight:700;text-transform:uppercase;color:#64748b;">Login email</p>
                                        <p style="margin:0;font-size:15px;line-height:22px;font-weight:700;color:#0f172a;word-break:break-word;">{{ $loginEmail }}</p>
                                    </td>
                                </tr>
                            </table>

                            @if ($updatedPassword)
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 22px;background-color:#f0fdf4;border:1px solid #a7f3d0;border-radius:6px;">
                                    <tr>
                                        <td style="padding:16px 20px;">
                                            <p style="margin:0 0 6px;font-size:11px;line-height:16px;font-weight:700;text-transform:uppercase;color:#166534;">Updated password</p>
                                            <p style="margin:0;font-family:Consolas,Monaco,monospace;font-size:18px;line-height:26px;font-weight:700;color:#14532d;word-break:break-all;">{{ $updatedPassword }}</p>
                                        </td>
                                    </tr>
                                </table>
                                <p style="margin:0 0 20px;font-size:14px;line-height:22px;color:#334155;">
                                    Sign in with these details and change this password as soon as possible. Do not share or forward this email.
                                </p>
                            @else
                                <p style="margin:0 0 20px;font-size:14px;line-height:22px;color:#334155;">
                                    Your password was not changed. Continue signing in with your existing password.
                                </p>
                            @endif

                            <p style="margin:0;font-size:13px;line-height:21px;color:#64748b;">
                                If you did not expect this change, contact TrabaGo support as soon as possible.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px;border-top:1px solid #e2e8f0;background-color:#f8fafc;">
                            <p style="margin:0;font-size:11px;line-height:18px;color:#64748b;">TrabaGo Cebu · Department of Manpower Development and Placement</p>
                            <p style="margin:4px 0 0;font-size:11px;line-height:18px;color:#94a3b8;">This is an automated account security message. Please do not reply.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>