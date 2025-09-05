import React, { useRef } from 'react';
import ReCAPTCHA from 'react-google-recaptcha';

export default function Formulario() {
  const recaptchaRef = useRef();

  const handleSubmit = (e) => {
    e.preventDefault();
    const token = recaptchaRef.current.getValue();
    console.log('Token do reCAPTCHA:', token);

    // Envie o token para o backend Laravel
    fetch('/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ name: 'Teste', email: 'teste@email.com', 'g-recaptcha-response': token }),
    });
  };

  return (
    <form onSubmit={handleSubmit}>
      <input type="text" placeholder="Nome" name="name" />
      <input type="email" placeholder="Email" name="email" />
      <ReCAPTCHA
        sitekey={import.meta.env.VITE_NOCAPTCHA_SITEKEY} // defina no .env
        ref={recaptchaRef}
      />
      <button type="submit">Enviar</button>
    </form>
  );
}
