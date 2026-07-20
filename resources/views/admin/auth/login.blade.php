<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | {{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #eef4f8;
            --panel: #ffffff;
            --text: #172033;
            --muted: #667085;
            --line: #d9e1ec;
            --accent: #0f766e;
            --danger: #b42318;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 20px;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, Arial, Helvetica, sans-serif;
        }

        .login-card {
            width: min(100%, 430px);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 28px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }

        p {
            margin: 0 0 22px;
            color: var(--muted);
        }

        form {
            display: grid;
            gap: 14px;
        }

        label {
            display: grid;
            gap: 6px;
            font-weight: 600;
        }

        input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 11px 12px;
            font: inherit;
        }

        .check {
            display: flex;
            gap: 8px;
            align-items: center;
            color: var(--muted);
            font-weight: 400;
        }

        .check input {
            width: auto;
        }

        button {
            border: 0;
            border-radius: 8px;
            padding: 12px 14px;
            background: var(--accent);
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        .error {
            margin-bottom: 16px;
            color: var(--danger);
        }
    </style>
</head>
<body>
    <section class="login-card">
        <h1>Admin Login</h1>
        <p>Secure access for authorized staff only.</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </label>

            <label>
                Password
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <label class="check">
                <input type="checkbox" name="remember" value="1">
                Remember this device
            </label>

            <button type="submit">Login</button>
        </form>
    </section>
</body>
</html>
