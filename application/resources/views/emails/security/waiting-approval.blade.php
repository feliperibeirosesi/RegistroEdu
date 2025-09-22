@extends('emails.layout')

@section('header')
    <h1>Aguardando Aprovação</h1>
@endsection

@section('content')
    <h2>Email confirmado com sucesso!</h2>
    <p>Olá, {{ $name }}!</p>

    <div class="alert alert-success">
        <strong>Email verificado!</strong> Seu endereço de email foi confirmado com sucesso.
    </div>

    <p>Agora seu registro será analisado por nossa equipe de segurança. Este processo é necessário para garantir a segurança de todos os usuários.</p>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3>O que acontece agora?</h3>
        <ul>
            <li>Seu email foi confirmado</li>
            <li>Nossa equipe está analisando seu registro</li>
            <li>Você receberá um email quando a análise for concluída</li>
            <li>Tempo estimado: <strong>{{ $estimated_time }}</strong></li>
        </ul>
    </div>

    <p>Agradecemos sua paciência! Em breve você poderá acessar a plataforma.</p>
@endsection
