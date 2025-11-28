<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color: #111;">
    <div style="max-width: 640px; margin: 24px auto; padding: 24px; border: 1px solid #eee; border-radius: 8px;">
      <h2 style="margin:0 0 12px;">Verifikasi Email</h2>
      <p>Halo {{ $name }},</p>
      <p>Terima kasih telah mendaftar di Mons Magna. Silakan klik tombol di bawah untuk memverifikasi alamat email Anda:</p>
      <p style="text-align:center; margin:24px 0;"><a href="{{ route('verification.verify', $token) }}" style="background:#2563eb;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;">Verifikasi Email</a></p>
      <p>Jika tombol tidak berfungsi, salin dan tempel tautan ini di browser Anda:</p>
      <p style="word-break:break-all;color:#555;">{{ route('verification.verify', $token) }}</p>
      <p>Salam,<br/>Tim Mons Magna</p>
    </div>
  </body>
</html>
