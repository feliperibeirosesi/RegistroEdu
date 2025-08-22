import React, { useEffect } from 'react';
import { useApi } from '../hooks/useApi';  // Ajuste o caminho conforme seu projeto
import { useError } from '../contexts/ErrorContext';

export default function TesteErro() {
  const { apiRequest } = useApi();
  const { error, clearError } = useError();

  useEffect(() => {
    clearError(); // Limpa erros anteriores ao montar

    async function testarErro() {
      // Faz requisição para endpoint que deve dar erro
      const data = await apiRequest('/teste-erro');

      if (data) {
        console.log('Dados recebidos:', data);
      }
    }

    testarErro();
  }, [apiRequest, clearError]);

  return (
    <div style={{ padding: '1rem' }}>
      <h1>Teste de Captura Global de Erro</h1>
      {error ? (
        <div style={{ color: 'red', marginTop: '1rem' }}>
          <h2>Erro Detectado:</h2>
          <p><strong>Código:</strong> {error.code}</p>
          <p><strong>Mensagem:</strong> {error.message}</p>
          {error.customDescription && <p><strong>Descrição:</strong> {error.customDescription}</p>}
        </div>
      ) : (
        <p>Aguardando resposta da API...</p>
      )}
    </div>
  );
}
