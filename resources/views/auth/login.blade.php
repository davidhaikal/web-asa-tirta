<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            color-scheme: light;
            --bg: #f7f6f2;
            --card: #ffffff;
            --ink: #1d2433;
            --muted: #6b7280;
            --accent: #127369;
            --accent-2: #e8f3f1;
            --danger: #b42318;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Space Grotesk", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(900px circle at 10% 10%, #e6f1ef 0, #f7f6f2 50%, #f3efe8 100%);
            color: var(--ink);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }
        .shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 24px;
        }
        .panel {
            background: var(--card);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(17, 24, 39, 0.12);
            animation: rise 0.6s ease-out both;
        }
        .brand {
            display: grid;
            gap: 14px;
        }
        .brand h1 {
            font-size: 32px;
            line-height: 1.1;
        }
        .brand p {
            color: var(--muted);
            font-size: 14px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-2);
            color: var(--accent);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            width: fit-content;
        }
        form {
            display: grid;
            gap: 16px;
            margin-top: 8px;
        }
        label {
            font-size: 13px;
            font-weight: 600;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            background: #fafafa;
        }
        input:focus {
            outline: 2px solid rgba(18, 115, 105, 0.2);
            border-color: var(--accent);
            background: #ffffff;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-size: 13px;
        }
        .row a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }
        .error {
            color: var(--danger);
            font-size: 12px;
            margin-top: 6px;
        }
        button {
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            background: var(--accent);
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        button:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(18, 115, 105, 0.25); }
        .side {
            background: linear-gradient(145deg, #0f1f2b, #18344a 55%, #0f1f2b 100%);
            color: #f8fafc;
            border-radius: 16px;
            padding: 28px;
            display: grid;
            gap: 18px;
            animation: rise 0.8s ease-out both;
        }
        .side h2 { font-size: 22px; line-height: 1.3; }
        .side ul { display: grid; gap: 10px; font-size: 13px; color: #d1d5db; }
        .side li { list-style: none; }
        .side strong { color: #ffffff; }
        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
        }
        @keyframes rise {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="panel">
            <div class="brand">
                <span class="badge">ASA Tirta</span>
                <h1>Masuk ke dashboard</h1>
                <p>Gunakan email dan password yang terdaftar untuk melanjutkan.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <label><input type="checkbox" name="remember"> Remember me</label>
                    <a href="{{ route('register') }}">Buat akun</a>
                </div>
                <button type="submit">Login</button>
            </form>
        </section>

        <aside class="side">
            <h2>Kontrol produksi lebih rapi dan cepat.</h2>
            <ul>
                <li><strong>QC:</strong> cek kualitas dan status produksi.</li>
                <li><strong>Gudang:</strong> pantau stok dan pengiriman.</li>
                <li><strong>Keuangan:</strong> ringkas invoice harian.</li>
            </ul>
            <p style="font-size: 12px; color: #9ca3af;">Butuh akun? Hubungi admin untuk akses awal.</p>
        </aside>
    </div>
</body>
</html>