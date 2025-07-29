<!DOCTYPE html>
<html lang="pt-br">
<head>
    @include('partials.head', [
        'title' => 'RegistroEdu - Sistema de Registro Educacional',
        'page' => 'Register',
        'css' => 'Register',
        'additionalCss' => ['partials/profile-menu']
    ])
</head>
<body>
    <div class="container">
        @include('partials.profile-menu', [
            'menuRole' => 'menu',
            'menuHidden' => 'true',
            'customLinks' => [
                ['url' => '/', 'label' => 'Home']
            ]
        ])

        <div class="forms">
            <h1>RegistroEdu</h1>

            <p class="authorization-warning">
                Atenção: o acesso ao site será permitido somente após autorização de um administrador.
            </p>
            <a type="submit" href="{{ url('auth/google') }}" class="google-btn" id="google-btn">
                <img src="{{ asset('assets/register/google-icon.png') }}" alt="Google" />
                Entrar com Google
            </a>
        </div>
    </div>

    @include('partials.scripts.profile-menu')
    <script src="{{ asset('js/Register.js') }}"></script>
</body>
</html>
