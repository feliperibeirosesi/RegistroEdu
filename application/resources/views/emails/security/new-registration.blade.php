@extends('emails.layout')

@section('header')
    <h1>🔔 Nova Solicitação de Registro</h1>
@endsection

@section('content')
    <h2>Nova solicitação pendente</h2>
    <p>Um novo usuário solicita acesso ao sistema:</p>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3>👤 Dados do Usuário</h3>
        <p><strong>Nome:</strong> {{ $user_name }}</p>
        <p><strong>Email:</strong> {{ $user_email }}</p>
        <p><strong>Domínio:</strong> {{ $user_domain }}</p>
        <p><strong>Data do Registro:</strong> {{ $registration_date }}</p>
    </div>

    <div class="alert alert-warning">
        <strong>Ação Necessária:</strong> Esta solicitação precisa ser aprovada ou rejeitada.
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $review_url }}" class="btn" style="margin-right: 10px;">Ver Detalhes</a>
        <a href="{{ $approve_url }}" class="btn btn-success" style="margin-right: 10px;">Aprovar</a>
        <a href="{{ $reject_url }}" class="btn btn-danger">Rejeitar</a>
    </div>

    <p><small>Você pode revisar todas as solicitações pendentes no painel administrativo.</small></p>
@endsection
