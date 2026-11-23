<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALCON - Employee Login</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #0f172a; }
        .login-card { background: #fff; width: 100%; max-width: 420px; border-radius: 10px; overflow: hidden; box-shadow: 0 20px 25px rgba(0,0,0,0.3); }
        .header { background: #111827; color: #fff; padding: 24px; text-align: center; border-bottom: 4px solid #f59e0b; }
        .header h1 { margin: 0; letter-spacing: 3px; font-size: 1.8rem; }
        .header p { margin: 6px 0 0; color: #f59e0b; font-size: 0.8rem; font-weight: bold; letter-spacing: 1px; }
        .body { padding: 28px; }
        .group { margin-bottom: 16px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
        input { width: 100%; padding: 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; }
        input:focus { outline: none; border-color: #f59e0b; }
        button { width: 100%; padding: 12px; background: #f59e0b; color: #111827; border: none; border-radius: 6px; font-weight: 700; font-size: 1rem; cursor: pointer; margin-top: 8px; }
        button:hover { background: #d97706; }
        .error { background: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 10px; font-size: 0.85rem; margin-bottom: 16px; border-radius: 4px; }
        .back-link { display: block; text-align: center; margin-top: 16px; font-size: 0.85rem; color: #64748b; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="header">
            <h1>HALCON</h1>
            <p>EMPLOYEE ACCESS PORTAL</p>
        </div>
        <div class="body">
            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="group">
                    <label for="login">Username or Email</label>
                    <input type="text" id="login" name="login" value="{{ old('login') }}" placeholder="rmonter or ricardo.admin@halcon.com" required autofocus>
                </div>
                <div class="group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit">Login</button>
            </form>

            <a href="{{ url('/track-order') }}" class="back-link">← Customer Order Tracking</a>
        </div>
    </div>
</body>
</html>