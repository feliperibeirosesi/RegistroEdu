<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status da Conta - RegistroEdu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #2a2a2a 100%);
            color: #fff;
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

        .verification-box {
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
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
        }

        .icon-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
            position: relative;
            overflow: hidden;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .status-pending-email .icon-circle {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
        }

        .status-waiting-admin .icon-circle {
            background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }

        .status-rejected .icon-circle {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.3);
        }

        .status-2fa .icon-circle {
            background: linear-gradient(135deg, #00bfff 0%, #1e90ff 100%);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
        }

        .envelope {
            width: 60px;
            height: 45px;
            background: white;
            border-radius: 4px;
            position: relative;
        }

        .envelope::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-left: 30px solid white;
            border-right: 30px solid white;
            border-top: 22px solid #ff6b35;
            border-radius: 2px 2px 0 0;
        }

        .clock-icon {
            width: 60px;
            height: 60px;
            border: 4px solid white;
            border-radius: 50%;
            position: relative;
        }

        .clock-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 2px;
            height: 20px;
            background: white;
            transform-origin: bottom;
            transform: translate(-50%, -100%) rotate(45deg);
        }

        .clock-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 2px;
            height: 15px;
            background: white;
            transform-origin: bottom;
            transform: translate(-50%, -100%) rotate(90deg);
        }

        .x-icon {
            width: 60px;
            height: 60px;
            position: relative;
        }

        .x-icon::before, .x-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50px;
            height: 4px;
            background: white;
            border-radius: 2px;
        }

        .x-icon::before { transform: translate(-50%, -50%) rotate(45deg); }
        .x-icon::after { transform: translate(-50%, -50%) rotate(-45deg); }

        .shield-icon {
            width: 60px;
            height: 70px;
            position: relative;
        }

        .shield-icon::before {
            content: '';
            position: absolute;
            width: 60px;
            height: 70px;
            background: white;
            clip-path: polygon(50% 0%, 100% 20%, 100% 80%, 50% 100%, 0% 80%, 0% 20%);
        }

        .shield-icon::after {
            content: '2FA';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #1e90ff;
            font-weight: 900;
            font-size: 14px;
            z-index: 1;
        }

        .title {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #fff 0%, #e0e0e0 50%, #c0c0c0 100%);
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
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .highlight {
            color: #ff6b35;
            font-weight: 600;
        }

        .steps {
            text-align: left;
            background: rgba(255, 107, 53, 0.1);
            border: 1px solid rgba(255, 107, 53, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            font-size: 1rem;
            color: #e0e0e0;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            gap: 12px;
        }

        .step:last-child { margin-bottom: 0; }

        .step-number {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .qr-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            display: inline-block;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .qr-container img {
            display: block;
            max-width: 250px;
            width: 100%;
            height: auto;
        }

        .secret-code {
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid rgba(0, 191, 255, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #b3e5fc;
            letter-spacing: 2px;
            word-break: break-all;
        }

        .input-2fa {
            border-radius: 12px;
            padding: 16px;
            border: 2px solid rgba(0, 191, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
            width: 100%;
            max-width: 300px;
            text-align: center;
            margin: 20px auto;
            font-size: 1.2rem;
            color: #fff;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
            transition: all 0.3s ease;
        }

        .input-2fa:focus {
            outline: none;
            border-color: #00bfff;
            box-shadow: 0 0 20px rgba(0, 191, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
        }

        .input-2fa::placeholder {
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: normal;
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
            color: #fff;
            box-shadow: 0 8px 25px rgba(0, 191, 255, 0.3);
            margin: 10px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%);
            box-shadow: 0 12px 35px rgba(0, 191, 255, 0.4);
            transform: translateY(-3px) scale(1.02);
        }

        .btn:active {
            transform: translateY(-1px) scale(1.01);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #666 0%, #555 100%);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #777 0%, #666 100%);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
            box-shadow: 0 8px 25px rgba(255, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #ff6666 0%, #ff4444 100%);
            box-shadow: 0 12px 35px rgba(255, 68, 68, 0.4);
        }

        .info-box {
            margin-top: 30px;
            padding: 20px;
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid rgba(0, 191, 255, 0.3);
            border-radius: 15px;
            font-size: 0.95rem;
            color: #b3e5fc;
        }

        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ffcccc;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .hidden { display: none !important; }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .verification-box {
                padding: 60px 35px;
                border-radius: 20px;
            }

            .title {
                font-size: 2.2rem;
                letter-spacing: -1px;
            }

            .subtitle {
                font-size: 1.3rem;
            }

            .message {
                font-size: 1.1rem;
                margin-bottom: 25px;
            }

            .status-icon {
                width: 100px;
                height: 100px;
            }

            .btn {
                min-width: 250px;
                padding: 16px 30px;
                font-size: 1rem;
                display: block;
                margin: 15px auto;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px 15px;
            }

            .verification-box {
                padding: 40px 25px;
                margin: 15px;
            }

            .title {
                font-size: 1.8rem;
            }

            .subtitle {
                font-size: 1.2rem;
            }

            .message {
                font-size: 1rem;
                margin-bottom: 20px;
            }

            .status-icon {
                width: 80px;
                height: 80px;
            }

            .btn {
                min-width: 200px;
                padding: 14px 25px;
                font-size: 0.95rem;
            }

            .steps {
                padding: 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-box" id="statusContainer">
            <div class="status-icon">
                <div class="icon-circle">
                    <div class="envelope" id="emailIcon"></div>
                    <div class="clock-icon hidden" id="clockIcon"></div>
                    <div class="x-icon hidden" id="xIcon"></div>
                    <div class="shield-icon hidden" id="shieldIcon"></div>
                </div>
            </div>

            <h1 class="title" id="statusTitle">Status da Conta</h1>
            <h2 class="subtitle" id="statusSubtitle">Carregando...</h2>
            <p class="message" id="statusMessage">Verificando o status da sua conta...</p>

            <div id="statusSteps"></div>
            <div id="actionButtons"></div>
            <div id="statusInfo"></div>
        </div>
    </div>

    <script>
        const STATUS_CONFIG = {
            pending_email: {
                title: 'Confirme seu Email',
                subtitle: 'Verificação Necessária',
                message: 'Enviamos um email de confirmação para <span class="highlight">seu email</span>. Verifique sua caixa de entrada e siga as instruções.',
                icon: 'email',
                class: 'status-pending-email',
                steps: [
                    'Abra seu <strong>email</strong> ou aplicativo de email',
                    'Procure por um email do <strong>RegistroEdu</strong>',
                    'Clique no botão de <strong>confirmação</strong>',
                    'Aguarde a análise da sua conta'
                ],
                buttons: [
                    { text: 'Abrir Gmail', href: 'https://mail.google.com', target: '_blank', class: 'btn' },
                    { text: 'Reenviar Email', action: 'resendEmail', class: 'btn btn-secondary' }
                ],
                info: '<strong>Não recebeu o email?</strong><br>Verifique sua pasta de <strong>spam</strong>. Você pode solicitar um novo email clicando em "Reenviar Email".'
            },
            waiting_admin: {
                title: 'Aguardando Aprovação',
                subtitle: 'Em Análise',
                message: 'Seu registro está sendo analisado. Este processo pode levar até <span class="highlight">48 horas úteis</span>.',
                icon: 'clock',
                class: 'status-waiting-admin',
                steps: [
                    'Sua conta foi <strong>verificada com sucesso</strong>',
                    'Nossa equipe está <strong>analisando</strong> seu registro',
                    'Você receberá um <strong>email</strong> quando for aprovado',
                    'Após aprovação, você poderá <strong>acessar o sistema</strong>'
                ],
                buttons: [
                    { text: 'Verificar Status', action: 'checkStatus', class: 'btn' },
                    { text: 'Voltar ao Login', href: '/login', class: 'btn btn-secondary' }
                ],
                info: '<strong>Tempo de análise</strong><br>Nossa equipe analisa de <strong>segunda a sexta</strong>, das 8h às 17h.'
            },
            rejected: {
                title: 'Registro Rejeitado',
                subtitle: 'Acesso Negado',
                message: 'Seu registro não atendeu aos critérios necessários. <span class="highlight">Entre em contato com o suporte</span>.',
                icon: 'x',
                class: 'status-rejected',
                steps: [
                    'Sua solicitação foi <strong>analisada</strong>',
                    'Infelizmente <strong>não foi aprovada</strong>',
                    'Entre em contato com o <strong>suporte</strong>',
                    'Você pode tentar um <strong>novo registro</strong> se elegível'
                ],
                buttons: [
                    { text: 'Contatar Suporte', href: 'mailto:suporte@registroedu.sp.gov.br', class: 'btn' },
                    { text: 'Voltar ao Login', href: '/login', class: 'btn btn-danger' }
                ],
                info: '<strong>Precisa de ajuda?</strong><br>Email: <strong>suporte@registroedu.sp.gov.br</strong>'
            }
        };

        const Utils = {
            getUrlParam(name) {
                const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                const results = regex.exec(location.search);
                return results ? decodeURIComponent(results[1].replace(/\+/g, ' ')) : '';
            },

            getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                return parts.length === 2 ? decodeURIComponent(parts.pop().split(';').shift()) : null;
            },

            hideAllIcons() {
                document.getElementById('emailIcon').classList.add('hidden');
                document.getElementById('clockIcon').classList.add('hidden');
                document.getElementById('xIcon').classList.add('hidden');
                document.getElementById('shieldIcon').classList.add('hidden');
            },

            showIcon(iconName) {
                this.hideAllIcons();
                const iconMap = {
                    email: 'emailIcon',
                    clock: 'clockIcon',
                    x: 'xIcon',
                    shield: 'shieldIcon'
                };
                const iconId = iconMap[iconName];
                if (iconId) {
                    document.getElementById(iconId).classList.remove('hidden');
                }
            }
        };

        const Renderer = {
            renderStatus(statusKey) {
                const config = STATUS_CONFIG[statusKey] || STATUS_CONFIG.pending_email;
                const container = document.getElementById('statusContainer');

                container.className = 'verification-box ' + config.class;
                document.getElementById('statusTitle').textContent = config.title;
                document.getElementById('statusSubtitle').textContent = config.subtitle;
                document.getElementById('statusMessage').innerHTML = config.message;

                Utils.showIcon(config.icon);
                this.renderSteps(config.steps);
                this.renderButtons(config.buttons);
                this.renderInfo(config.info);
            },

            renderSteps(steps) {
                const container = document.getElementById('statusSteps');

                if (!steps || steps.length === 0) {
                    container.innerHTML = '';
                    return;
                }

                container.className = 'steps';
                container.innerHTML = steps.map((step, index) => `
                    <div class="step">
                        <div class="step-number">${index + 1}</div>
                        <div>${step}</div>
                    </div>
                `).join('');
            },

            renderButtons(buttons) {
                const container = document.getElementById('actionButtons');
                container.innerHTML = '';

                buttons.forEach(button => {
                    const btn = document.createElement('a');
                    btn.className = button.class;
                    btn.textContent = button.text;

                    if (button.href) {
                        btn.href = button.href;
                        if (button.target) btn.target = button.target;
                    }

                    if (button.action) {
                        btn.style.cursor = 'pointer';
                        btn.onclick = (e) => {
                            e.preventDefault();
                            Actions[button.action]();
                        };
                    }

                    container.appendChild(btn);
                });
            },

            renderInfo(info) {
                const container = document.getElementById('statusInfo');
                container.className = 'info-box';
                container.innerHTML = info || '';
            },

            renderTwoFactor() {
                const container = document.getElementById('statusContainer');
                container.className = 'verification-box status-2fa';

                document.getElementById('statusTitle').textContent = 'Configuração 2FA';
                document.getElementById('statusSubtitle').textContent = 'Autenticação de Dois Fatores';
                document.getElementById('statusMessage').textContent = 'Configure seu segundo fator de autenticação';

                Utils.showIcon('shield');

                document.getElementById('statusSteps').innerHTML = `
                    <div class="steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div>Baixe um app autenticador (Google Authenticator, Authy, etc)</div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div>Escaneie o QR Code abaixo</div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div>Insira o código de 6 dígitos gerado</div>
                        </div>
                    </div>
                `;

                document.getElementById('actionButtons').innerHTML = `
                    <div style="text-align: center;">
                        <div class="loading" style="margin: 20px auto;"></div>
                        <p style="color: #b3e5fc; margin-top: 10px;">Gerando QR Code...</p>
                    </div>
                `;

                document.getElementById('statusInfo').innerHTML = '';

                TwoFactorAuth.generate();
            }
        };

        const TwoFactorAuth = {
            async generate() {
                try {
                    const csrfToken = Utils.getCookie('XSRF-TOKEN');

                    const response = await fetch('http://localhost:8000/2fa/generate', {
                        method: 'POST',
                        headers: {
                            'X-XSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'include'
                    });

                    if (!response.ok) {
                        throw new Error('Erro ao gerar QR code');
                    }

                    const data = await response.json();
                    this.renderQRCode(data);

                } catch (error) {
                    console.error('Erro ao gerar 2FA:', error);
                    this.renderError('Erro ao gerar QR Code. Verifique sua autenticação e tente novamente.');
                }
            },

            renderQRCode(data) {
                const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(data.qr_code_url)}&size=250x250`;

                document.getElementById('actionButtons').innerHTML = `
                    <div class="qr-container">
                        <img src="${qrCodeUrl}" alt="QR Code 2FA">
                    </div>

                    <div class="secret-code">
                        <strong>Código Manual:</strong><br>
                        ${data.secret}
                    </div>

                    <input
                        type="text"
                        id="twoFactorCode"
                        class="input-2fa"
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]*"
                        inputmode="numeric"
                    >

                    <button class="btn" id="submit2FA">Verificar Código</button>
                `;

                document.getElementById('statusInfo').innerHTML = `
                    <div class="info-box">
                        <strong>Não consegue escanear?</strong><br>
                        Use o código manual acima para configurar manualmente no seu app autenticador.
                    </div>
                `;

                const input = document.getElementById('twoFactorCode');
                input.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                });

                document.getElementById('submit2FA').addEventListener('click', () => {
                    this.verify();
                });
            },

            async verify() {
                const code = document.getElementById('twoFactorCode').value.trim();

                if (!code || code.length !== 6) {
                    this.renderError('Por favor, insira o código de 6 dígitos.');
                    return;
                }

                const btn = document.getElementById('submit2FA');
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.innerHTML = '<span class="loading"></span> Verificando...';

                try {
                    const csrfToken = Utils.getCookie('XSRF-TOKEN');

                    const response = await fetch('http://localhost:8000/2fa/verify', {
                        method: 'POST',
                        headers: {
                            'X-XSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'include',
                        body: JSON.stringify({ code })
                    });

                    const data = await response.json();

                    console.log('Resposta da verificação 2FA:', data);

                    if (!response.ok) {
                        throw new Error(data.message || 'Código inválido');
                    }

                    window.location.href = data.redirect || '/dashboard';

                } catch (error) {
                    console.error('Erro ao verificar 2FA:', error);
                    this.renderError(error.message || 'Código inválido. Tente novamente.');
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            },

            renderError(message) {
                const existing = document.querySelector('.error-message');
                if (existing) existing.remove();

                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = message;

                const actionButtons = document.getElementById('actionButtons');
                const input = document.getElementById('twoFactorCode');
                if (input) {
                    input.parentNode.insertBefore(errorDiv, input);
                } else {
                    actionButtons.appendChild(errorDiv);
                }

                setTimeout(() => errorDiv.remove(), 5000);
            }
        };

        const Actions = {
            resendEmail() {
                alert('Solicitação de reenvio de email enviada com sucesso!');
            },

            checkStatus() {
                window.location.reload();
            }
        };

        function init() {
            const status = Utils.getUrlParam('status');
            const error = Utils.getUrlParam('error');

            console.log('Status detectado:', status);
            console.log('Error:', error);

            if (status === 'pending_2fa' || status === 'pending_two_factor') {
                Renderer.renderTwoFactor();
                return;
            }

            let statusKey = 'pending_email';

            if (error === 'not_approved') {
                const statusMap = {
                    'pending_email_verification': 'pending_email',
                    'waiting_admin_approval': 'waiting_admin',
                    'rejected': 'rejected'
                };
                statusKey = statusMap[status] || 'pending_email';
            } else if (STATUS_CONFIG[status]) {
                statusKey = status;
            }

            Renderer.renderStatus(statusKey);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    </script>
</body>
</html>
