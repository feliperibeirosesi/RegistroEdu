import { useError } from '../contexts/ErrorContext';

// hook personalizado para requisições API
export const useApi = () => {
  const { showError } = useError(); // pega função para exibir erros

  const apiRequest = async (url, options = {}) => {
    try {
      const response = await fetch(url, {
        headers: { 'Content-Type': 'application/json', ...options.headers },
        ...options
      });

      if (!response.ok) {
        // tenta extrair dados do erro
        const errorData = await response.json().catch(() => ({}));

        // define mensagens amigáveis e descrições baseadas no status HTTP
        const getErrorMessage = (status) => { /* ... */ };
        const getErrorDescription = (status, errorData) => { /* ... */ };

        // exibe o erro usando o contexto global
        showError({
          code: response.status,
          message: errorData.message || getErrorMessage(response.status),
          customDescription: getErrorDescription(response.status, errorData)
        });

        return null; // retorna null em caso de erro
      }

      return await response.json(); // retorna dados se tiver tudo certo
    } catch (error) {
      // captura erros de rede
      showError({
        code: 'NETWORK_ERROR',
        message: 'Erro de conexão',
        customDescription: 'Verifique sua conexão com a internet e tente novamente.'
      });

      return null;
    }
  };

  return { apiRequest }; // retorna função
};
