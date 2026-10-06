<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SIKAT')</title>

    {{-- FAVICON --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* =========================================================
           BACKGROUND — GRADIENT + BLOB + GRID
        ========================================================== */
        .auth-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 45%, #eef2ff 100%);
        }

        .auth-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(139, 92, 246, 0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139, 92, 246, 0.07) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse at center, black 25%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 25%, transparent 75%);
        }

        .auth-blob-1 {
            position: absolute;
            width: 480px; height: 480px;
            top: -140px; left: -140px;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.8;
            background: radial-gradient(circle, #c084fc 0%, transparent 65%);
        }
        .auth-blob-2 {
            position: absolute;
            width: 480px; height: 480px;
            bottom: -140px; right: -140px;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.8;
            background: radial-gradient(circle, #a5b4fc 0%, transparent 65%);
        }
        .auth-blob-3 {
            position: absolute;
            width: 320px; height: 320px;
            top: 45%; left: 50%;
            transform: translateX(-50%);
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.55;
            background: radial-gradient(circle, #f0abfc 0%, transparent 65%);
        }

        /* =========================================================
           CONTAINER
        ========================================================== */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 460px;
        }

        /* =========================================================
           LOGO
        ========================================================== */
        .auth-logo {
            display: block;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .auth-logo img {
            width: 160px;
            height: auto;
            object-fit: contain;
            margin: 0 auto;
        }

        /* =========================================================
           CARD
        ========================================================== */
        .auth-card {
            background: #ffffff;
            border: 1px solid #ede9fe;
            border-radius: 24px;
            padding: 2.25rem 2rem;
            box-shadow:
                0 1px 3px rgba(15, 23, 42, 0.04),
                0 20px 50px -10px rgba(139, 92, 246, 0.18),
                0 0 0 1px rgba(139, 92, 246, 0.04);
        }

        /* =========================================================
           TITLE
        ========================================================== */
        .auth-welcome-title {
            font-size: 1.625rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #0f172a;
            line-height: 1.2;
        }
        .auth-welcome-title span {
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .auth-welcome-description {
            margin-top: 0.5rem;
            font-size: 0.9375rem;
            color: #64748b;
            line-height: 1.5;
        }

        /* =========================================================
           ALERT
        ========================================================== */
        .auth-alert {
            margin-top: 1.25rem;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            line-height: 1.4;
            border: 1px solid transparent;
        }
        .auth-alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
        .auth-alert-warning { background: #fffbeb; border-color: #fde68a; color: #b45309; }
        .auth-alert-error   { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

        /* =========================================================
           FORM
        ========================================================== */
        .auth-form {
            margin-top: 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .auth-field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .auth-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
        }
        .auth-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.9375rem;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }
        .auth-input::placeholder { color: #94a3b8; }
        .auth-input:focus {
            border-color: #a855f7;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.12);
        }

        /* =========================================================
           PASSWORD + EYE
        ========================================================== */
        .auth-password { position: relative; }
        .auth-password .auth-input { padding-right: 3rem; }
        .auth-eye {
            position: absolute;
            top: 0; right: 0; bottom: 0;
            padding-right: 1rem;
            display: flex;
            align-items: center;
            color: #94a3b8;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s;
        }
        .auth-eye:hover { color: #a855f7; }

        /* =========================================================
           BUTTON
        ========================================================== */
        .auth-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.95rem 1.25rem;
            margin-top: 0.25rem;
            border-radius: 12px;
            font-size: 0.9375rem;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.35);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .auth-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(139, 92, 246, 0.45);
        }
        .auth-button:active { transform: translateY(0); }

        /* =========================================================
           LINK
        ========================================================== */
        .auth-register {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .auth-register p {
            font-size: 0.875rem;
            color: #64748b;
        }
        .auth-register a {
            font-weight: 600;
            color: #9333ea;
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-register a:hover { color: #7e22ce; }

        /* =========================================================
           FOOTER
        ========================================================== */
        .auth-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* =========================================================
           MOBILE
        ========================================================== */
        @media (max-width: 640px) {
            .auth-container {
                padding: 1.5rem 1rem;
                align-items: flex-start;
                padding-top: 2.5rem;
            }
            .auth-card {
                padding: 1.75rem 1.5rem;
                border-radius: 20px;
            }
            .auth-welcome-title { font-size: 1.375rem; }
            .auth-logo img { width: 130px; }
            .auth-blob-1,
            .auth-blob-2 { width: 320px; height: 320px; filter: blur(70px); }
            .auth-blob-3 { width: 220px; height: 220px; }
        }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">

    {{-- BACKGROUND --}}
    <div class="auth-bg">
        <div class="auth-blob-1"></div>
        <div class="auth-blob-2"></div>
        <div class="auth-blob-3"></div>
    </div>

    @yield('content')

</body>
</html>