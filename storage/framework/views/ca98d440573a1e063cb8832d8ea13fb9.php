<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php echo $__env->make('partials.head', [
        'title' => 'RegistroEdu - Sistema de Registro Educacional',
        'page' => 'Register',
        'css' => 'Register',
        'additionalCss' => ['partials/profile-menu']
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
    <div class="container">
        <?php echo $__env->make('partials.profile-menu', [
            'menuRole' => 'menu',
            'menuHidden' => 'true',
            'customLinks' => [
                ['url' => '/', 'label' => 'Home']
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="forms">
            <h1>RegistroEdu</h1>

            <p class="authorization-warning">
                Atenção: o acesso ao site será permitido somente após autorização de um administrador.
            </p>
            <a type="submit" href="<?php echo e(url('auth/google')); ?>" class="google-btn" id="google-btn">
                <img src="<?php echo e(asset('assets/register/google-icon.png')); ?>" alt="Google" />
                Entrar com Google
            </a>
        </div>
    </div>

    <?php echo $__env->make('partials.scripts.profile-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="<?php echo e(asset('js/Register.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\I2HM\Documents\GITHUB\RegistroEdu\resources\views/page/RegisterScreen.blade.php ENDPATH**/ ?>