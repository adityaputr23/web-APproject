<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Baru — APVISUALS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #0a0a0f;
            color: #e2e2e2;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
            background: #0a0a0f;
        }
        .header {
            background: linear-gradient(135deg, #0f0f1a 0%, #13131f 100%);
            border-bottom: 1px solid rgba(139, 92, 246, 0.3);
            padding: 36px 40px 28px;
            text-align: center;
        }
        .logo {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 3px;
            background: linear-gradient(135deg, #a78bfa, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 8px;
        }
        .header-sub {
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .badge {
            display: inline-block;
            margin-top: 16px;
            padding: 6px 16px;
            background: rgba(139, 92, 246, 0.12);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 20px;
            font-size: 12px;
            color: #c084fc;
            font-weight: 600;
        }
        .body {
            padding: 36px 40px;
        }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }
        .intro-text {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 28px;
            line-height: 1.6;
        }
        .info-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 28px;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            gap: 14px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(139, 92, 246, 0.8);
            min-width: 90px;
            padding-top: 1px;
        }
        .info-value {
            font-size: 14px;
            color: #e2e2e2;
            flex: 1;
            line-height: 1.6;
        }
        .message-box {
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.15);
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }
        .message-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(139, 92, 246, 0.8);
            margin-bottom: 12px;
        }
        .message-text {
            font-size: 14px;
            color: rgba(255,255,255,0.75);
            line-height: 1.75;
            white-space: pre-line;
        }
        .reply-btn {
            display: block;
            text-align: center;
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            color: #fff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-bottom: 28px;
        }
        .meta-info {
            font-size: 12px;
            color: rgba(255,255,255,0.25);
            text-align: center;
            line-height: 1.7;
        }
        .footer {
            background: rgba(255,255,255,0.015);
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 20px 40px;
            text-align: center;
        }
        .footer-text {
            font-size: 11px;
            color: rgba(255,255,255,0.2);
        }
        .footer-brand {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 2px;
            color: rgba(139, 92, 246, 0.5);
            text-transform: uppercase;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <div class="logo">APVISUALS</div>
        <div class="header-sub">Portfolio Notification System</div>
        <div class="badge">📩 Pesan Baru Masuk</div>
    </div>

    <!-- Body -->
    <div class="body">
        <div class="greeting">Halo, Aditya! 👋</div>
        <p class="intro-text">
            Ada pesan baru yang masuk melalui form kontak di portfolio kamu.<br>
            Detail lengkapnya ada di bawah ini.
        </p>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Dari</span>
                <span class="info-value"><strong style="color:#fff;">{{ $enquiry->name }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $enquiry->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Perihal</span>
                <span class="info-value">{{ $enquiry->subject ?? '(Tidak ada perihal)' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu</span>
                <span class="info-value">{{ $enquiry->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
        </div>

        <!-- Message -->
        <div class="message-box">
            <div class="message-title">💬 Isi Pesan</div>
            <div class="message-text">{{ $enquiry->message }}</div>
        </div>

        <!-- Reply Button -->
        <a href="mailto:{{ $enquiry->email }}?subject=Re: {{ rawurlencode($enquiry->subject ?? 'Balasan dari APVISUALS') }}" class="reply-btn">
            ↩ Balas ke {{ $enquiry->name }}
        </a>

        <p class="meta-info">
            Pesan ini juga tersimpan di Admin Dashboard APVISUALS.<br>
            Kamu bisa membaca dan mengelola semua pesan di sana.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-brand">APVISUALS</div>
        <p class="footer-text">© {{ date('Y') }} APVISUALS. Email notifikasi otomatis — jangan balas email ini langsung.</p>
    </div>
</div>
</body>
</html>
