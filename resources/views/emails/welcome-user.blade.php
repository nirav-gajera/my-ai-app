<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $appName }}</title>
</head>
<body style="margin:0;padding:0;background:#eef3f8;font-family:'Figtree',Arial,sans-serif;color:#10213a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef3f8;margin:0;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;">
                    <tr>
                        <td style="padding:0 18px 18px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:linear-gradient(135deg,#10213a 0%,#0f766e 100%);border-radius:28px;overflow:hidden;">
                                <tr>
                                    <td style="padding:30px 30px 26px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <div style="width:40px;height:40px;border-radius:14px;background:linear-gradient(135deg,#1d4ed8,#0f766e);display:flex;align-items:center;justify-content:center;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td style="padding-left:12px;">
                                                    <div style="font-family:'Space Grotesk',Arial,sans-serif;font-size:18px;font-weight:700;line-height:1.05;color:#ffffff;">{{ $appName }}</div>
                                                    <div style="font-size:12px;line-height:1.4;color:rgba(255,255,255,0.72);">Private knowledge workspace</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="margin-top:26px;">
                                            <div style="display:inline-block;padding:7px 12px;border-radius:999px;background:rgba(255,255,255,0.12);font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#ffffff;">
                                                Welcome aboard
                                            </div>
                                        </div>

                                        <h1 style="margin:18px 0 10px;font-family:'Space Grotesk',Arial,sans-serif;font-size:34px;line-height:1.02;letter-spacing:-0.04em;color:#ffffff;">
                                            Welcome, {{ $userName }}
                                        </h1>
                                        <p style="margin:0;max-width:470px;font-size:15px;line-height:1.7;color:rgba(255,255,255,0.82);">
                                            Your account is ready. You can now upload source material, index your knowledge, and ask grounded questions against your own documents.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 18px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:rgba(255,255,255,0.92);border:1px solid rgba(19,41,72,0.08);border-radius:28px;box-shadow:0 22px 50px rgba(15,23,42,0.08);overflow:hidden;">
                                <tr>
                                    <td style="padding:30px;">
                                        <h2 style="margin:0 0 12px;font-family:'Space Grotesk',Arial,sans-serif;font-size:22px;line-height:1.1;color:#10213a;">
                                            What you can do next
                                        </h2>
                                        <p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#5d708d;">
                                            Start building your private AI workspace with a few simple steps.
                                        </p>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:22px;">
                                            <tr>
                                                <td style="padding:0 0 12px;">
                                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fbff;border:1px solid rgba(19,41,72,0.08);border-radius:18px;">
                                                        <tr>
                                                            <td style="padding:16px 18px;">
                                                                <div style="font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#0f766e;">1. Index knowledge</div>
                                                                <div style="margin-top:6px;font-size:14px;line-height:1.65;color:#10213a;">Upload TXT, MD, CSV, JSON, or LOG files, or paste content directly into your knowledge base.</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 0 12px;">
                                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fbff;border:1px solid rgba(19,41,72,0.08);border-radius:18px;">
                                                        <tr>
                                                            <td style="padding:16px 18px;">
                                                                <div style="font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#1d4ed8;">2. Ask grounded questions</div>
                                                                <div style="margin-top:6px;font-size:14px;line-height:1.65;color:#10213a;">Create conversations and get responses backed by the chunks retrieved from your indexed sources.</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fbff;border:1px solid rgba(19,41,72,0.08);border-radius:18px;">
                                                        <tr>
                                                            <td style="padding:16px 18px;">
                                                                <div style="font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#355c7d;">3. Manage your workspace</div>
                                                                <div style="margin-top:6px;font-size:14px;line-height:1.65;color:#10213a;">Replace outdated documents, track users if you are an admin, and keep your answers aligned with the latest knowledge.</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <table role="presentation" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                                            <tr>
                                                <td>
                                                    <a href="{{ $appUrl }}" style="display:inline-block;padding:13px 20px;border-radius:999px;background:linear-gradient(135deg,#1d4ed8,#0f766e);color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">
                                                        Open {{ $appName }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="border-top:1px solid rgba(19,41,72,0.08);padding-top:18px;">
                                            <div style="font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#5d708d;">Quick links</div>
                                            <p style="margin:8px 0 0;font-size:13px;line-height:1.8;color:#5d708d;">
                                                <a href="{{ $knowledgeUrl }}" style="color:#1d4ed8;text-decoration:none;">Knowledge Base</a>
                                                &nbsp;•&nbsp;
                                                <a href="{{ $conversationUrl }}" style="color:#1d4ed8;text-decoration:none;">Conversations</a>
                                                &nbsp;•&nbsp;
                                                <a href="{{ $loginUrl }}" style="color:#1d4ed8;text-decoration:none;">Login</a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 24px 0;text-align:center;">
                            <p style="margin:0;font-size:12px;line-height:1.7;color:#71839d;">
                                This message was sent to welcome you to {{ $appName }}.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
