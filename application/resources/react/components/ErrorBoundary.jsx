import React from 'react';
import ErrorPage from './Error'; // Exibe o erro

class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    // Estado inicial sem erro
    this.state = { hasError: false, error: null };
  }

  // Método estático chamado quando um erro aparece em algum componente filho
  static getDerivedStateFromError(error) {

    // Atualiza o estado para indicar que ocorreu um erro
    return { hasError: true, error };
  }

  // Captura detalhes adicionais do erro para logs
  componentDidCatch(error, errorInfo) {
    console.error('ErrorBoundary capturou um erro:', error, errorInfo);
  }

  render() {
    // Se tiver erro exibe a página de erro personalizada
    if (this.state.hasError) {
      return <ErrorPage error={this.state.error} />;
    }

    // se não renderiza normalmente os filhos
    return this.props.children;
  }
}

export default ErrorBoundary;
