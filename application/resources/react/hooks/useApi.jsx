import { useError } from '../contexts/ErrorContext';

export const useApi = () => {
  const { showError } = useError();

  const apiRequest = async (url, options = {}) => {
    try {
      const response = await fetch(url, {
        headers: {
          'Content-Type': 'application/json',
          ...options.headers
        },
        ...options
      });

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));

        const getErrorMessage = (status) => {
          switch (status) {
            case 400: return 'Requisição inválida';
            case 401: return 'Não autorizado';
            case 403: return 'Acesso negado';
            case 404: return 'Recurso não encontrado';
            case 500: return 'Erro interno do servidor';
            case 502: return 'Servidor indisponível';
            case 503: return 'Serviço indisponível';
            default: return `Erro HTTP ${status}`;
          }
        };

        const getErrorDescription = (status, errorData) => {
          if (errorData.description || errorData.detail) {
            return errorData.description || errorData.detail;
          }

          switch (status) {
            case 400: return 'Verifique os dados enviados e tente novamente.';
            case 401: return 'Faça login novamente para continuar.';
            case 403: return 'Você não tem permissão para acessar este recurso.';
            case 404: return 'O recurso solicitado não foi encontrado.';
            case 500: return 'Nossos servidores estão com problemas. Tente novamente em alguns minutos.';
            case 502:
            case 503: return 'Nossos serviços estão temporariamente indisponíveis.';
            default: return 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
          }
        };

        showError({
          code: response.status,
          message: errorData.message || getErrorMessage(response.status),
          customDescription: getErrorDescription(response.status, errorData)
        });

        return null;
      }

      return await response.json();
    } catch (error) {
      showError({
        code: 'NETWORK_ERROR',
        message: 'Erro de conexão',
        customDescription: 'Verifique sua conexão com a internet e tente novamente.'
      });

      return null;
    }
  };

  return { apiRequest };
};
