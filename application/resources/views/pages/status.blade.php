<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status da Conta - RegistroEdu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body, html { margin:0; padding:0; width:100%; height:100%; font-family:"Inter", sans-serif; background: linear-gradient(135deg,#0a0a0a 0%,#1a1a1a 50%,#2a2a2a 100%); color:#fff; scroll-behavior:smooth; min-height:100vh; overflow-x:hidden; }
        .container { display:flex; align-items:center; justify-content:center; min-height:100vh; padding:40px 20px; }
        .verification-box { background: rgba(26,26,26,0.98); backdrop-filter: blur(20px); border-radius:25px; border:1px solid rgba(64,64,64,0.3); box-shadow:0 25px 50px rgba(0,0,0,0.4); padding:80px 50px; text-align:center; max-width:600px; width:100%; position:relative; animation:slideInUp 0.8s ease-out; }
        @keyframes slideInUp { from {opacity:0; transform:translateY(50px);} to {opacity:1; transform:translateY(0);} }
        .status-icon { width:120px; height:120px; margin:0 auto 2rem; }
        .icon-circle { width:100%; height:100%; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 30px rgba(255,107,53,0.3); position:relative; overflow:hidden; animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100% {transform:scale(1);} 50% {transform:scale(1.05);} }
        .status-pending-email .icon-circle { background: linear-gradient(135deg,#ff6b35 0%,#f7931e 100%); }
        .status-waiting-admin .icon-circle { background: linear-gradient(135deg,#ffd700 0%,#ffb347 100%); box-shadow:0 10px 30px rgba(255,215,0,0.3); }
        .status-rejected .icon-circle { background: linear-gradient(135deg,#ff4444 0%,#cc0000 100%); box-shadow:0 10px 30px rgba(255,68,68,0.3); }
        .status-approved .icon-circle { background: linear-gradient(135deg,#00ff7f 0%,#00bfff 100%); box-shadow:0 10px 30px rgba(0,191,255,0.3); }
        .envelope { width:60px; height:45px; background:white; border-radius:4px; position:relative; }
        .envelope::before { content:''; position:absolute; top:0; left:0; width:0; height:0; border-left:30px solid white; border-right:30px solid white; border-top:22px solid #ff6b35; border-radius:2px 2px 0 0; }
        .envelope::after { content:''; position:absolute; top:8px; left:50%; transform:translateX(-50%); width:8px; height:8px; background:#ff6b35; border-radius:50%; animation:blink 1.5s infinite; }
        @keyframes blink { 0%,50% {opacity:1;} 51%,100% {opacity:0;} }
        .clock-icon { width:60px; height:60px; border:4px solid white; border-radius:50%; position:relative; }
        .clock-icon::before { content:''; position:absolute; top:50%; left:50%; width:2px; height:20px; background:white; transform-origin:bottom; transform:translate(-50%,-100%) rotate(45deg); }
        .clock-icon::after { content:''; position:absolute; top:50%; left:50%; width:2px; height:15px; background:white; transform-origin:bottom; transform:translate(-50%,-100%) rotate(90deg); }
        .x-icon { width:60px; height:60px; position:relative; }
        .x-icon::before, .x-icon::after { content:''; position:absolute; top:50%; left:50%; width:50px; height:4px; background:white; border-radius:2px; }
        .x-icon::before { transform:translate(-50%,-50%) rotate(45deg); }
        .x-icon::after { transform:translate(-50%,-50%) rotate(-45deg); }
        .title { font-size:3rem; font-weight:900; margin-bottom:15px; background: linear-gradient(135deg,#fff 0%,#e0e0e0 50%,#c0c0c0 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; letter-spacing:-2px; line-height:0.9; }
        .subtitle { font-size:1.5rem; font-weight:600; margin-bottom:25px; color:#e0e0e0; letter-spacing:0.5px; }
        .message { font-size:1.2rem; color:#c0c0c0; line-height:1.6; margin-bottom:30px; max-width:500px; margin-left:auto; margin-right:auto; }
        .highlight { color:#ff6b35; font-weight:600; }
        .steps { text-align:left; background: rgba(255,107,53,0.1); border:1px solid rgba(255,107,53,0.3); border-radius:15px; padding:25px; margin:30px 0; font-size:1rem; color:#e0e0e0; }
        .status-waiting-admin .steps { background: rgba(255,215,0,0.1); border-color: rgba(255,215,0,0.3); }
        .status-rejected .steps { background: rgba(255,68,68,0.1); border-color: rgba(255,68,68,0.3); }
        .status-approved .steps { background: rgba(0,191,255,0.1); border-color: rgba(0,191,255,0.3); }
        .step { display:flex; align-items:flex-start; margin-bottom:15px; gap:12px; }
        .step:last-child { margin-bottom:0; }
        .step-number { background: linear-gradient(135deg,#ff6b35 0%,#f7931e 100%); color:white; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:600; flex-shrink:0; margin-top:2px; }
        .status-waiting-admin .step-number { background: linear-gradient(135deg,#ffd700 0%,#ffb347 100%); }
        .status-rejected .step-number { background: linear-gradient(135deg,#ff4444 0%,#cc0000 100%); }
        .status-approved .step-number { background: linear-gradient(135deg,#00ff7f 0%,#00bfff 100%); }
        .btn { padding:18px 35px; border-radius:15px; border:none; font-size:1.1rem; font-weight:600; cursor:pointer; min-width:200px; display:inline-flex; align-items:center; justify-content:center; gap:10px; text-decoration:none; position:relative; overflow:hidden; background: linear-gradient(135deg,#00bfff 0%,#1e90ff 100%); color:#fff; box-shadow:0 8px 25px rgba(0,191,255,0.3); margin:10px; transition:all 0.3s ease; }
        .btn:hover { background: linear-gradient(135deg,#1e90ff 0%,#4682b4 100%); box-shadow:0 12px 35px rgba(0,191,255,0.4); transform:translateY(-3px) scale(1.02); }
        .btn:active { transform:translateY(-1px) scale(1.01); }
        .btn-secondary { background: linear-gradient(135deg,#666 0%,#555 100%); box-shadow:0 8px 25px rgba(0,0,0,0.3); }
        .btn-secondary:hover { background: linear-gradient(135deg,#777 0%,#666 100%); box-shadow:0 12px 35px rgba(0,0,0,0.4); }
        .btn-danger { background: linear-gradient(135deg,#ff4444 0%,#cc0000 100%); box-shadow:0 8px 25px rgba(255,68,68,0.3); }
        .btn-danger:hover { background: linear-gradient(135deg,#ff6666 0%,#ff4444 100%); box-shadow:0 12px 35px rgba(255,68,68,0.4); }
        .resend-info { margin-top:30px; padding:20px; background: rgba(0,191,255,0.1); border:1px solid rgba(0,191,255,0.3); border-radius:15px; font-size:0.95rem; color:#b3e5fc; }
        .status-waiting-admin .resend-info { background: rgba(255,215,0,0.1); border-color: rgba(255,215,0,0.3); color:#fff8dc; }
        .status-rejected .resend-info { background: rgba(255,68,68,0.1); border-color: rgba(255,68,68,0.3); color:#ffcccc; }
        .status-approved .resend-info { background: rgba(0,191,255,0.1); border-color: rgba(0,191,255,0.3); color:#b3e5fc; }
        @media(max-width:768px){.verification-box{padding:60px 35px;border-radius:20px;}.title{font-size:2.2rem;letter-spacing:-1px;}.subtitle{font-size:1.3rem;}.message{font-size:1.1rem;margin-bottom:25px;}.status-icon{width:100px;height:100px;}.envelope{width:50px;height:37px;}.envelope::before{border-left-width:25px;border-right-width:25px;border-top-width:18px;}.btn{min-width:250px;padding:16px 30px;font-size:1rem;display:block;margin:15px auto;}}
        @media(max-width:480px){.container{padding:20px 15px;}.verification-box{padding:40px 25px;margin:15px;}.title{font-size:1.8rem;}.subtitle{font-size:1.2rem;}.message{font-size:1rem;margin-bottom:20px;}.status-icon{width:80px;height:80px;}.envelope{width:40px;height:30px;}.btn{min-width:200px;padding:14px 25px;font-size:0.95rem;}.steps{padding:20px;font-size:0.9rem;}}
        .hidden { display:none !important; }
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
                </div>
            </div>

            <h1 class="title" id="statusTitle">Status da Conta</h1>
            <h2 class="subtitle" id="statusSubtitle">Carregando...</h2>
            <p class="message" id="statusMessage">Verificando o status da sua conta...</p>

            <div class="steps" id="statusSteps"></div>
            <div id="actionButtons"></div>
            <div class="resend-info" id="statusInfo"></div>
        </div>
    </div>

    <script>
        const statusConfig = {
            'pending_email': {
                title: 'Confirme seu Email',
                subtitle: 'Verificação Necessária',
                message: 'Enviamos um email de confirmação para <span class="highlight">seu email</span>. Verifique sua caixa de entrada e siga as instruções para ativar sua conta.',
                icon: 'email',
                class: 'status-pending-email',
                steps: [
                    'Abra seu <strong>email</strong> ou aplicativo de email',
                    'Procure por um email do <strong>RegistroEdu</strong>',
                    'Clique no botão de <strong>confirmação</strong> dentro do email',
                    'Aguarde a análise da sua conta por nossa equipe'
                ],
                buttons: [
                    { text: 'Abrir Gmail', href: 'https://mail.google.com', target: '_blank', class: 'btn' },
                    { text: 'Reenviar Email', onclick: 'resendEmail()', class: 'btn btn-secondary' }
                ],
                info: '<strong>Não recebeu o email?</strong><br>Verifique sua pasta de <strong>spam</strong> ou <strong>lixo eletrônico</strong>. Você pode solicitar um novo email de confirmação clicando em "Reenviar Email".'
            },
            'waiting_admin': {
                title: 'Aguardando Aprovação',
                subtitle: 'Em Análise',
                message: 'Seu registro está sendo analisado por nossa equipe. Este processo pode levar até <span class="highlight">48 horas úteis</span>.',
                icon: 'clock',
                class: 'status-waiting-admin',
                steps: [
                    'Sua conta foi <strong>verificada com sucesso</strong>',
                    'Nossa equipe está <strong>analisando</strong> seu registro',
                    'Você receberá um <strong>email</strong> quando for aprovado',
                    'Após aprovação, você poderá <strong>acessar o sistema</strong>'
                ],
                buttons: [
                    { text: 'Verificar Status', onclick: 'checkStatus()', class: 'btn' },
                    { text: 'Voltar ao Login', href: '/login', class: 'btn btn-secondary' }
                ],
                info: '<strong>Tempo de análise</strong><br>Nossa equipe analisa as solicitações de <strong>segunda a sexta-feira</strong>, das 8h às 17h. Você será notificado por email assim que sua conta for aprovada.'
            },
            'rejected': {
                title: 'Registro Rejeitado',
                subtitle: 'Acesso Negado',
                message: 'Seu registro foi rejeitado por não atender aos critérios necessários. <span class="highlight">Entre em contato com o suporte</span> para mais informações.',
                icon: 'x',
                class: 'status-rejected',
                steps: [
                    'Sua solicitação foi <strong>analisada</strong> pela equipe',
                    'Infelizmente, <strong>não foi aprovada</strong>',
                    'Entre em contato com o <strong>suporte</strong> para esclarecimentos',
                    'Você pode tentar um <strong>novo registro</strong> se elegível'
                ],
                buttons: [
                    { text: 'Contatar Suporte', href: 'mailto:suporte@registroedu.sp.gov.br', class: 'btn' },
                    { text: 'Voltar ao Login', href: '/login', class: 'btn btn-danger' }
                ],
                info: '<strong>Precisa de ajuda?</strong><br>Entre em contato com nosso suporte através do email <strong>suporte@registroedu.sp.gov.br</strong> informando seu email de registro para mais esclarecimentos.'
            },
            'default': {
                title: 'Status da Conta',
                subtitle: 'Verificando...',
                message: 'Seu registro está pendente. <span class="highlight">Aguarde mais informações</span>.',
                icon: 'email',
                class: 'status-pending-email',
                steps: [
                    'Seu registro está sendo <strong>processado</strong>',
                    'Aguarde <strong>instruções por email</strong>',
                    'Verifique periodicamente seu <strong>email</strong>',
                    'Entre em contato se precisar de <strong>ajuda</strong>'
                ],
                buttons: [
                    { text: 'Recarregar', onclick: 'window.location.reload()', class: 'btn' },
                    { text: 'Voltar ao Login', href: '/login', class: 'btn btn-secondary' }
                ],
                info: '<strong>Dúvidas?</strong><br>Se você não recebeu nenhuma informação ou está com dúvidas, entre em contato com nosso suporte.'
            }
        };

        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        function updatePageStatus() {
            const status = getUrlParameter('status');
            const error = getUrlParameter('error');

            console.log('Status:', status, 'Error:', error);

            let statusKey = 'default';

            if (error === 'not_approved') {
                switch(status) {
                    case 'pending_email_verification': statusKey = 'pending_email'; break;
                    case 'waiting_admin_approval': statusKey = 'waiting_admin'; break;
                    case 'rejected': statusKey = 'rejected'; break;
                }
            } else if (status) {
                if (statusConfig[status]) statusKey = status;
            }

            const config = statusConfig[statusKey];

            const container = document.getElementById('statusContainer');
            container.className = 'verification-box ' + config.class;

            document.getElementById('statusTitle').textContent = config.title;
            document.getElementById('statusSubtitle').textContent = config.subtitle;
            document.getElementById('statusMessage').innerHTML = config.message;

            document.getElementById('emailIcon').className = config.icon === 'email' ? 'envelope' : 'envelope hidden';
            document.getElementById('clockIcon').className = config.icon === 'clock' ? 'clock-icon' : 'clock-icon hidden';
            document.getElementById('xIcon').className = config.icon === 'x' ? 'x-icon' : 'x-icon hidden';

            const stepsContainer = document.getElementById('statusSteps');
            stepsContainer.innerHTML = '';
            config.steps.forEach((step,index)=>{
                const stepDiv = document.createElement('div');
                stepDiv.className = 'step';
                stepDiv.innerHTML = `<div class="step-number">${index+1}</div><div>${step}</div>`;
                stepsContainer.appendChild(stepDiv);
            });

            const buttonsContainer = document.getElementById('actionButtons');
            buttonsContainer.innerHTML = '';
            config.buttons.forEach(button=>{
                const btn = document.createElement('a');
                btn.className = button.class;
                btn.textContent = button.text;
                if(button.href){ btn.href = button.href; if(button.target) btn.target=button.target; }
                if(button.onclick) btn.onclick = new Function(button.onclick);
                buttonsContainer.appendChild(btn);
            });

            document.getElementById('statusInfo').innerHTML = config.info;
        }

        async function resendEmail() {
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Reenviando...';
            button.style.pointerEvents = 'none';

            try {
                const res = await fetch('{{ route("account.resend-verification") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt')
                        },
                    body: JSON.stringify({})
                });

                const data = await res.json();
                if (res.ok) {
                    button.textContent = 'Email Reenviado!';
                } else {
                    button.textContent = 'Erro ao Reenviar';
                    console.error(data);
                }
            } catch (err) {
                console.error(err);
                button.textContent = 'Erro de Rede';
            }

            setTimeout(() => {
                button.textContent = originalText;
                button.style.pointerEvents = 'auto';
            }, 2000);
        }

        async function checkStatus() {
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Verificando...';
            button.style.pointerEvents = 'none';

            try {
                const res = await fetch('{{ route("account.check-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt')
                    },
                    body: JSON.stringify({})
                });

                const data = await res.json();
                if (res.ok) {
                    const newStatus = data.status || 'default';
                    window.location.search = `?status=${newStatus}&error=not_approved`;
                } else {
                    console.error(data);
                    alert('Erro ao verificar status da conta');
                }
            } catch (err) {
                console.error(err);
                alert('Erro de rede ao verificar status');
            }

            button.textContent = originalText;
            button.style.pointerEvents = 'auto';
        }

        document.addEventListener('DOMContentLoaded', ()=>{
            updatePageStatus();
            console.log('RegistroEdu - Página de Status da Conta carregada');
        });
    </script>
</body>
</html>
