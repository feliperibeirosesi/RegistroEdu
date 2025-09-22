@extends('emails.layout')

@section('header')
    <h1>❌ Registro Não Aprovado</h1>
@endsection

@section('content')
    <h2>Solicitação não aprovada</h2>
    <p>Olá, {{ $name }}.</p>

    <p>Infelizmente, sua solicitação de registro no RegistroEdu não foi aprovada.</p>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3>📋 Detalhes</h3>
        <p><strong>Analisado por:</strong> {{ $rejected_by }}</p>
        <p><strong>Data da análise:</strong> {{ $rejected_at }}</p>
        <p><strong>Motivo:</strong></p>
        <div style="background: white; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0;">
            {{ $reason }}
        </div>
    </div>

    <div class="alert alert-info">
        <strong>💬 Precisa de esclarecimentos?</strong> Entre em contato conosco em {{ $contact_email }}
    </div>

    <p>Se você acredita que houve algum engano ou tem dúvidas sobre esta decisão, não hesite em nos contatar.</p>
@endsection
