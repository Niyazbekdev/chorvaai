<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email tasdiqlash</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; padding: 40px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .logo { font-size: 22px; font-weight: 700; color: #16a34a; margin-bottom: 24px; }
        h2 { color: #1f2937; margin: 0 0 8px; }
        p { color: #6b7280; font-size: 15px; line-height: 1.6; }
        .code-box { background: #f0fdf4; border: 2px solid #86efac; border-radius: 10px; padding: 20px; text-align: center; margin: 24px 0; }
        .code { font-size: 36px; font-weight: 700; letter-spacing: 0.5em; color: #15803d; font-family: monospace; }
        .note { font-size: 13px; color: #9ca3af; margin-top: 4px; }
        .footer { border-top: 1px solid #e5e7eb; margin-top: 32px; padding-top: 16px; font-size: 13px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🐄 ChorvaAI</div>
        <h2>Email manzilingizni tasdiqlang</h2>
        <p>Quyidagi 6 xonali kodni kiriting. Kod 5 daqiqa davomida amal qiladi.</p>

        <div class="code-box">
            <div class="code">{{ $code }}</div>
            <div class="note">Ushbu kodni hech kimga bermang</div>
        </div>

        <p>Agar siz ro'yxatdan o'tmagan bo'lsangiz, ushbu xatni e'tiborsiz qoldiring.</p>

        <div class="footer">ChorvaAI — Chorvachilik platformasi</div>
    </div>
</body>
</html>
