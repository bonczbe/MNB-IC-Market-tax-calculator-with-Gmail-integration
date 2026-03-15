<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Maintenance – {{ config('app.name', 'Laravel') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #0f172a;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(99, 102, 241, 0.08) 0%, transparent 50%);
            color: white;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            text-align: center;
            padding: 3rem 2rem;
            max-width: 560px;
        }

        .icon-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 2.5rem;
        }

        .icon-ring {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(99,102,241,0.1));
            border: 1px solid rgba(59, 130, 246, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.1);
        }

        .gear {
            font-size: 52px;
            animation: spin 6s linear infinite;
            display: inline-block;
            filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.5));
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .eyebrow {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #3b82f6;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.2rem;
            background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        p {
            font-size: 1.05rem;
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 2.5rem;
        }

        p span {
            color: #94a3b8;
        }

        .divider {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #6366f1);
            margin: 0 auto 2.5rem;
            border-radius: 2px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            padding: 0.5rem 1.2rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            backdrop-filter: blur(8px);
        }

        .dot {
            width: 7px;
            height: 7px;
            background-color: #3b82f6;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .footer {
            margin-top: 3rem;
            font-size: 0.78rem;
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrapper">
            <div class="icon-ring">
                <span class="gear">⚙️</span>
            </div>
        </div>

        <div class="eyebrow">Scheduled Maintenance</div>

        <h1>We'll be right<br>back shortly.</h1>

        <div class="divider"></div>

        <p>
            We're upgrading our systems to bring you<br>
            <span>a faster and better experience.</span><br>
            This won't take long — please check back soon.
        </p>

        <div class="badge">
            <span class="dot"></span>
            Work in progress
        </div>
    </div>
</body>
</html>
