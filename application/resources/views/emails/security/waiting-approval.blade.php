@extends('emails.layout')

@section('header')
    <h1 style="margin:0; font-size:24px; color:#333;">Aguardando Aprovação</h1>
@endsection

@section('content')
    <h2 style="color:#28a745; margin-top:0;">Email confirmado com sucesso!</h2>
    <p style="font-size:16px; color:#555;">Olá, {{ $name }}!</p>

    <div style="background:#d4edda; border:1px solid #c3e6cb; padding:15px; border-radius:6px; margin:20px 0; font-size:15px; color:#155724;">
        <strong>Email verificado!</strong> Seu endereço de email foi confirmado com sucesso.
    </div>

    <p style="font-size:15px; color:#555; line-height:1.5;">
        Agora seu registro será analisado por nossa equipe de segurança.
        Este processo é necessário para garantir a segurança de todos os usuários.
    </p>

    <div style="background:#f8f9fa; padding:20px; border-radius:8px; margin:20px 0; border:1px solid #ddd;">
        <h3 style="margin-top:0; color:#333;">O que acontece agora?</h3>
        <ul style="padding-left:18px; color:#555; font-size:15px;">
            <li>Seu email foi confirmado</li>
            <li>Nossa equipe está analisando seu registro</li>
            <li>Você receberá um email quando a análise for concluída</li>
            <li>Tempo estimado: <strong>{{ $estimated_time }}</strong></li>
        </ul>
    </div>

    <p style="font-size:15px; color:#555;">Agradecemos sua paciência! Em breve você poderá acessar a plataforma.</p>
@endsection
