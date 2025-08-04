<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @viteReactRefresh
    @vite('resources/react/Main.jsx')
    <title>RegistroEdu</title>
</head>
<body>
    <style>
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: #111;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            font-family: 'Segoe UI', sans-serif;
        }

        .spinner {
            display: flex;
            gap: 0.6rem;
            margin-bottom: 1.2rem;
        }

        .dot {
            width: 1rem;
            height: 1rem;
            background-color: #4fc3f7;
            border-radius: 50%;
            animation: bounce 0.6s infinite ease-in-out;
        }

        .dot1 {
            animation-delay: 0s;
        }

        .dot2 {
            animation-delay: 0.2s;
        }

        .dot3 {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }

        .loading-text {
            font-size: 1.2rem;
            color: #ccc;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
    </style>

    <div id="preloader">
        <div class="spinner">
            <div class="dot dot1"></div>
            <div class="dot dot2"></div>
            <div class="dot dot3"></div>
        </div>
        <p class="loading-text">Carregando...</p>
    </div>

    <div id="root"></div>
</body>
</html>
