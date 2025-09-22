@extends('emails.layout')

@section('content')
    <h1 class="title">Olá, {{ $name }}!</h1>
    <h2 class="subtitle">Confirmação de Email</h2>

    <p>
        Para confirmar seu email, clique no botão abaixo. Esse link expira em <strong>{{ $expires_in }} horas</strong>.
    </p>

    <p style="text-align:center; margin:30px 0;">
        <a href="{{ $verification_url }}" class="btn">
           Confirmar Email
        </a>
    </p>

    <p>
        Se você não criou uma conta, ignore este email.
    </p>
@endsection
