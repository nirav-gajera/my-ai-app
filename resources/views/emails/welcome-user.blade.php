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
                                                                <div style="margin-top:6px;font-size:14px;line-height:1.65;color:#10213a;">Upload TXT, MD, CSV, JSON, PDF or LOG files, or paste content directly into your knowledge base.</div>
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
                                                                <div style="margin-top:6px;font-size:14px;line-height:1.65;color:#10213a;">Chat with your data. Every response is cited with sources from your own indexed documents.</div>
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
                                                                <div style="font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#7c3aed;">3. Telegram Integration</div>
                                                                <div style="margin-top:6px;font-size:14px;line-height:1.65;color:#10213a;">Link your account to our Telegram bot to chat and upload documents on the go.</div>
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
                                                                <div style="font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#355c7d;">4. Manage your workspace</div>
                                                                <div style="margin-top:6px;font-size:14px;line-height:1.65;color:#10213a;">Replace outdated documents, track users if you are an admin, and keep your answers aligned with the latest knowledge.</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        @if($telegramBotUrl)
                                        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:20px;padding:20px;margin-bottom:24px;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td style="vertical-align:top;width:40px;">
                                                        <div style="width:32px;height:32px;background:#0088cc;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#ffffff;font-size:18px;font-weight:bold;">T</div>
                                                    </td>
                                                    <td style="padding-left:12px;">
                                                        <h3 style="margin:0 0 4px;font-size:16px;color:#0369a1;">Chat on Telegram</h3>
                                                        <p style="margin:0 0 12px;font-size:13px;color:#0ea5e9;">Access your knowledge base from anywhere via our secure bot.</p>
                                                        <a href="{{ $telegramBotUrl }}" style="display:inline-block;padding:8px 16px;background:#0088cc;color:#ffffff;text-decoration:none;border-radius:8px;font-size:13px;font-weight:600;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 6px; margin-top: -2px;" viewBox="0 0 16 16">
                                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/>
                                                            </svg>
                                                            Link Telegram
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        @endif

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
