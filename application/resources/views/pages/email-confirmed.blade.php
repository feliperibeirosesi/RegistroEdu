<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmado com Sucesso</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #2a2a2a 100%);
            color: #ffffff;
            scroll-behavior: smooth;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .confirmation-box {
            background: rgba(26, 26, 26, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            border: 1px solid rgba(64, 64, 64, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            padding: 80px 50px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            position: relative;
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
        }

        .check-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #00bfff 0%, #1e90ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .checkmark {
            width: 50px;
            height: 30px;
            border: 5px solid white;
            border-top: none;
            border-right: none;
            transform: rotate(-45deg);
            z-index: 1;
        }

        .title {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 50%, #c0c0c0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -2px;
            line-height: 0.9;
        }

        .subtitle {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: #e0e0e0;
            letter-spacing: 0.5px;
        }

        .message {
            font-size: 1.2rem;
            color: #c0c0c0;
            line-height: 1.6;
            margin-bottom: 50px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            padding: 18px 35px;
            border-radius: 15px;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            min-width: 200px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #00bfff 0%, #1e90ff 100%);
            color: #ffffff;
            box-shadow: 0 8px 25px rgba(0, 191, 255, 0.3);
        }

        .btn:hover {
            background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%);
            box-shadow: 0 12px 35px rgba(0, 191, 255, 0.4);
            transform: translateY(-3px) scale(1.02);
        }

        .btn:active {
            transform: translateY(-1px) scale(1.01);
        }

        @media (max-width: 768px) {
            .confirmation-box { padding: 60px 35px; border-radius: 20px; }
            .title { font-size: 2.2rem; letter-spacing: -1px; }
            .subtitle { font-size: 1.3rem; }
            .message { font-size: 1.1rem; margin-bottom: 40px; }
            .success-icon { width: 100px; height: 100px; }
            .checkmark { width: 40px; height: 25px; border-width: 4px; }
            .btn { min-width: 250px; padding: 16px 30px; font-size: 1rem; }
        }

        @media (max-width: 480px) {
            .container { padding: 20px 15px; }
            .confirmation-box { padding: 40px 25px; margin: 15px; }
            .title { font-size: 1.8rem; }
            .subtitle { font-size: 1.2rem; }
            .message { font-size: 1rem; margin-bottom: 35px; }
            .success-icon { width: 80px; height: 80px; }
            .btn { min-width: 200px; padding: 14px 25px; font-size: 0.95rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-box">
            <div class="success-icon">
                <div class="check-circle">
                    <div class="checkmark"></div>
                </div>
            </div>

            <h1 class="title">Email Confirmado!</h1>
            <h2 class="subtitle">Verificação Concluída</h2>
            <p class="message">
                Parabéns! Seu email foi verificado com sucesso. Agora sua conta será analisada por um funcionário.
            </p>
            <a href="http://localhost:8000" class="btn">
                Ir ao site RegistroEdu
            </a>
        </div>
    </div>
</body>
</html>
