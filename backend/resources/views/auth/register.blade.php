<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account - LeadRadar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        :root {
            --bg-outer: #e9ece4;
            --card-white: #ffffff;
            --lime-primary: #a3f23a;
            --lime-hover: #92e02e;
            --lime-dark: #19380d;
            --text-dark: #12180e;
            --text-muted: #737c6c;
            --border-subtle: #e3e7dc;
            --radius-pill: 9999px;
        }

        * {
            box-sizing: border-box;
            margin: 0; padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-outer);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: var(--card-white);
            border-radius: 28px;
            width: 100%;
            max-width: 460px;
            padding: 36px 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-subtle);
        }

        .auth-logo {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--lime-primary);
            color: var(--lime-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .auth-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .auth-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-size: 20px;
        }

        .form-control {
            width: 100%;
            background: #f5f7f0;
            border: 1px solid var(--border-subtle);
            padding: 12px 14px 12px 42px;
            border-radius: 14px;
            font-size: 14px;
            color: var(--text-dark);
            outline: none;
            transition: all 0.2s;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--lime-dark);
            box-shadow: 0 0 0 3px rgba(163, 242, 58, 0.25);
        }

        .btn-submit {
            width: 100%;
            background: var(--lime-primary);
            color: var(--lime-dark);
            border: none;
            padding: 14px;
            border-radius: var(--radius-pill);
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--lime-hover);
        }

        .auth-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--text-dark);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-logo">LR</div>
    <h1 class="auth-title">Create Admin Account</h1>
    <p class="auth-subtitle">Register to unlock full executive access to LeadRadar</p>

    @if($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <div class="input-wrapper">
                <span class="material-symbols-outlined input-icon">person</span>
                <input type="text" name="name" class="form-control" placeholder="Sarah Jenkins" value="{{ old('name') }}" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-wrapper">
                <span class="material-symbols-outlined input-icon">mail</span>
                <input type="email" name="email" class="form-control" placeholder="admin@leadradar.com" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrapper">
                <span class="material-symbols-outlined input-icon">lock</span>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <div class="input-wrapper">
                <span class="material-symbols-outlined input-icon">lock_reset</span>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-type password" required>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            Create Account <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
        </button>
    </form>

    <div class="auth-footer">
        Already registered? <a href="{{ route('login') }}">Sign In</a>
    </div>
</div>

</body>
</html>
