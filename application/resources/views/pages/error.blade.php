<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro - RegistroEdu</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            margin: auto;
            background: rgba(26,26,26,0.98);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-header {
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 100%);
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .error-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(30deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(30deg); }
        }

        .error-body {
            padding: 40px 30px;
            color: #c0c0c0;
            font-size: 16px;
            line-height: 1.6;
            text-align: center;
        }

        .error-footer {
            background: #1a1a1a;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #888888;
        }

        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ff4757;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .btn {
            display: inline-block;
            padding: 18px 35px;
            margin: 10px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            color: #ffffff;
            background: linear-gradient(135deg, #00bfff 0%, #1e90ff 100%);
            box-shadow: 0 8px 25px rgba(0,191,255,0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0,191,255,0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #666 0%, #555 100%);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #777 0%, #666 100%);
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
        }

        .title {
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 15px;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .subtitle {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #e0e0e0;
        }

        .error-details {
            background: rgba(255,71,87,0.1);
            border: 1px solid rgba(255,71,87,0.3);
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            font-size: 14px;
            color: #ffcdd2;
        }

        .countdown {
            font-size: 18px;
            font-weight: 600;
            color: #00bfff;
            margin-top: 20px;
        }

        @media (max-width: 640px) {
            body {
                padding: 10px;
            }

            .error-container {
                border-radius: 15px;
            }

            .error-body {
                padding: 30px 20px;
            }

            .title {
                font-size: 28px;
            }

            .subtitle {
                font-size: 18px;
            }

            .btn {
                display: block;
                margin: 15px 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            RegistroEdu
        </div>

        <div class="error-body">
            <div class="error-icon" id="errorIcon">⚠️</div>

            <div class="title" id="errorTitle">Ops! Algo deu errado</div>
            <div class="subtitle" id="errorSubtitle">Encontramos um problema inesperado</div>

            <div class="error-details" id="errorDetails">
                <strong>Código do erro:</strong> <span id="errorCode">UNKNOWN</span><br>
                <strong>Descrição:</strong> <span id="errorDescription">Erro não identificado</span>
            </div>

            <div>
                <a href="/" class="btn" id="homeBtn">
                    🏠 Voltar ao Início
                </a>
                <a href="#" class="btn btn-secondary" onclick="window.history.back(); return false;">
                    ↩️ Página Anterior
                </a>
            </div>

            <div class="countdown" id="countdown" style="display: none;">
                Redirecionando automaticamente em <span id="countdownTimer">10</span> segundos...
            </div>
        </div>

        <div class="error-footer">
            © <span id="currentYear"></span> RegistroEdu - Todos os direitos reservados
        </div>
    </div>

    <script>
        const errorTypes = {
            'oauth': {
                icon: '🔐',
                title: 'Erro de Autenticação',
                subtitle: 'Falha ao conectar com o provedor de login',
                description: 'Não foi possível completar a autenticação OAuth. Verifique suas credenciais e tente novamente.',
                code: 'AUTH_001'
            },
            'permission': {
                icon: '🚫',
                title: 'Acesso Negado',
                subtitle: 'Você não tem permissão para acessar esta página',
                description: 'Seu usuário não possui as permissões necessárias para acessar este recurso.',
                code: 'AUTH_002'
            },
            'network': {
                icon: '🌐',
                title: 'Erro de Conexão',
                subtitle: 'Problemas de conectividade detectados',
                description: 'Não foi possível estabelecer conexão com o servidor. Verifique sua internet.',
                code: 'NET_001'
            },
            'server': {
                icon: '🔧',
                title: 'Erro Interno do Servidor',
                subtitle: 'Nossos servidores estão passando por dificuldades',
                description: 'Erro interno no servidor. Nossa equipe foi notificada e está trabalhando na solução.',
                code: 'SRV_001'
            },
            'notfound': {
                icon: '🔍',
                title: 'Página Não Encontrada',
                subtitle: 'A página que você procura não existe',
                description: 'A URL solicitada não foi encontrada em nossos servidores.',
                code: 'HTTP_404'
            },
            'maintenance': {
                icon: '🛠️',
                title: 'Sistema em Manutenção',
                subtitle: 'Estamos melhorando nossos serviços',
                description: 'O sistema está temporariamente indisponível para manutenção programada.',
                code: 'MAINT_001'
            }
        };

        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        function initErrorPage() {
            const errorType = getUrlParameter('error') || 'unknown';
            const error = errorTypes[errorType] || errorTypes['server'];

            document.getElementById('errorIcon').textContent = error.icon;
            document.getElementById('errorTitle').textContent = error.title;
            document.getElementById('errorSubtitle').textContent = error.subtitle;
            document.getElementById('errorCode').textContent = error.code;
            document.getElementById('errorDescription').textContent = error.description;
            document.getElementById('currentYear').textContent = new Date().getFullYear();

            document.title = `${error.title} - RegistroEdu`;

            if (['maintenance', 'server'].includes(errorType)) {
                startCountdown();
            }
        }

        function startCountdown() {
            const countdownEl = document.getElementById('countdown');
            const timerEl = document.getElementById('countdownTimer');
            let seconds = 10;

            countdownEl.style.display = 'block';

            const interval = setInterval(() => {
                seconds--;
                timerEl.textContent = seconds;

                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = '/';
                }
            }, 1000);
        }

        document.addEventListener('mousemove', (e) => {
            const container = document.querySelector('.error-container');
            const rect = container.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const deltaX = (e.clientX - centerX) / 50;
            const deltaY = (e.clientY - centerY) / 50;

            container.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
        });

        document.addEventListener('mouseleave', () => {
            const container = document.querySelector('.error-container');
            container.style.transform = 'translate(0, 0)';
        });

        document.addEventListener('DOMContentLoaded', initErrorPage);

        console.log('RegistroEdu - Página de Erro carregada');
        console.log('Tipo de erro:', getUrlParameter('error'));
    </script>
</body>
</html>
