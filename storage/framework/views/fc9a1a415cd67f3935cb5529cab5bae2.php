<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php echo $__env->make('partials.head', [
        'title' => 'RegistroEdu - Sistema de Registro Educacional',
        'page' => 'Home',
        'css' => 'Home',
        'additionalCss' => ['partials/profile-menu']
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
    <div class="container">
        <?php echo $__env->make('partials.top-bar', [
            'logoText' => 'RegistroEdu',
            'customLinks' => []
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="content">
            <div class="welcome-box">
                <h1>RegistroEdu</h1>
                <h2>Sistema de Registro Educacional</h2>
                <p>Plataforma moderna para gestão de documentos educacionais e registros acadêmicos.</p>

                <div class="features">
                    <div class="feature-item">
                        <i class="fas fa-file-alt"></i>
                        <span>Documentos Digitais</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Segurança Garantida</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <span>Acesso Controlado</span>
                    </div>
                </div>

                <div class="cta-buttons">
                    <?php if(auth()->guard()->guest()): ?>
                        <button class="btn btn-primary" onclick="window.location.href='/singin'">
                            <i class="fas fa-sign-in-alt"></i>
                            Fazer Login
                        </button>
                        <button class="btn btn-secondary" onclick="scrollToInfo()">
                            <i class="fas fa-info-circle"></i>
                            Saiba Mais
                        </button>
                    <?php else: ?>
                        <button class="btn btn-primary" onclick="window.location.href='/documentos'">
                            <i class="fas fa-folder-open"></i>
                            Acessar Documentos
                        </button>
                        <button class="btn btn-secondary" onclick="window.location.href='/perfil'">
                            <i class="fas fa-user"></i>
                            Meu Perfil
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="info-section" id="info-section">
            <div class="info-container">
                <h3>Como Funciona?</h3>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Cadastro</h4>
                            <p>Faça login com sua conta Google institucional</p>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Autorização</h4>
                            <p>Aguarde aprovação de um administrador</p>
                        </div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Acesso Completo</h4>
                            <p>Gerencie documentos e registros educacionais</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('partials.scripts.profile-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script>
        function scrollToInfo() {
            document.getElementById('info-section').scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\I2HM\Documents\GITHUB\RegistroEdu\resources\views/page/HomeScreen.blade.php ENDPATH**/ ?>