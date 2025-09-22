@extends('emails.layout')

@section('header')
    <h1>🎉 Registro Aprovado!</h1>
@endsection

@section('content')
    <h2>Bem-vindo ao RegistroEdu!</h2>
    <p>Olá, {{ $name }}!</p>

    <div class="alert alert-success">
        <strong>✅ Parabéns!</strong> Seu registro foi aprovado com sucesso!
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3>📋 Detalhes da Aprovação</h3>
        <p><strong>Aprovado por:</strong> {{ $approved_by }}</p>
        <p><strong>Data da aprovação:</strong> {{ $approved_at }}</p>
    </div>

    <p>Agora você tem acesso completo à plataforma RegistroEdu!</p>

    <h3>🚀 Próximos passos:</h3>
    <ul>
        <li>Faça login usando sua conta Google</li>
        <li>Complete seu perfil</li>
        <li>Explore todas as funcionalidades</li>
        <li>Entre em contato conosco se precisar de ajuda</li>
    </ul>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $login_url }}" class="btn btn-success">Fazer Login</a>
    </div>

    <p>Estamos muito felizes em ter você conosco! 🎓</p>
@endsection
