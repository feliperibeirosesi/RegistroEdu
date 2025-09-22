<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'RegistroEdu' }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background: rgba(26,26,26,0.98);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        }

        .email-header {
            background: linear-gradient(135deg, #00bfff 0%, #1e90ff 100%);
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
        }

        .email-body {
            padding: 40px 30px;
            color: #c0c0c0;
            font-size: 16px;
            line-height: 1.6;
        }

        .email-footer {
            background: #1a1a1a;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #888888;
        }

        .btn {
            display: inline-block;
            padding: 18px 35px;
            margin-top: 20px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            color: #ffffff;
            background: linear-gradient(135deg, #00bfff 0%, #1e90ff 100%);
            box-shadow: 0 8px 25px rgba(0,191,255,0.3);
        }

        .btn:hover {
            background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%);
        }

        .title {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .subtitle {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            RegistroEdu
        </div>

        <div class="email-body">
            @yield('content')
        </div>

        <div class="email-footer">
            © {{ date('Y') }} RegistroEdu - Todos os direitos reservados
        </div>
    </div>
</body>
</html>
