<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f2f8;
            --card: #ffffff;
            --ink: #1c1b24;
            --muted: #6b7280;
            --accent: #5b3fd0;
            --accent-2: #efeafd;
            --danger: #b42318;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Space Grotesk", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(900px circle at 90% 10%, #efeafd 0, #f6f2f8 45%, #f2f1ff 100%);
            color: var(--ink);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }
        .shell {
            width: min(1020px, 100%);
            display: grid;
            grid-template-columns: 1fr 1.1fr;
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
            gap: 12px;
        }
        .brand h1 {
            font-size: 30px;
            line-height: 1.15;
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
            margin-top: 10px;
        }
        label {
            font-size: 13px;
            font-weight: 600;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            background: #fafafa;
        }
        input:focus, select:focus {
            outline: 2px solid rgba(91, 63, 208, 0.2);
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
        button:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(91, 63, 208, 0.25); }
        .side {
            background: linear-gradient(145deg, #1b1537, #3b2c6b 55%, #1b1537 100%);
            color: #f9fafb;
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
        <aside class="side">
            <span class="badge">ASA Tirta</span>
            <h2>Daftar akun untuk mulai kelola produksi.</h2>
            <ul>
                <li><strong>Admin:</strong> kelola user dan konfigurasi.</li>
                <li><strong>Produksi:</strong> pantau batch harian.</li>
                <li><strong>Driver:</strong> lihat jadwal pengiriman.</li>
            </ul>
            <p style="font-size: 12px; color: #cbd5f5;">Pastikan role sesuai tugas agar akses tepat.</p>
        </aside>

        <section class="panel">
            <div class="brand">
                <span class="badge">Buat Akun</span>
                <h1>Isi data untuk mulai masuk sistem</h1>
                <p>Lengkapi informasi berikut untuk membuat akun baru.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div>
                    <label for="name">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                </div>
                <div>
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="qc">QC</option>
                        <option value="produksi">Produksi</option>
                        <option value="gudang">Gudang</option>
                        <option value="keuangan">Keuangan</option>
                        <option value="driver" selected>Driver</option>
                    </select>
                    @error('role')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <span style="color: var(--muted);">Sudah punya akun?</span>
                    <a href="{{ route('login') }}">Login</a>
                </div>
                <button type="submit">Register</button>
            </form>
        </section>
    </div>
</body>
</html>