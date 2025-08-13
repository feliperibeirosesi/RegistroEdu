import React, { createContext, useContext, useState } from 'react';

// cria um contexto que será compartilhado entre componentes
const ErrorContext = createContext();

export const ErrorProvider = ({ children }) => {
  // estado para armazenar o erro atual
  const [error, setError] = useState(null);

  // função para exibir um erro
  const showError = (errorData) => {
    setError({
      message: errorData.message || 'Algo deu errado', // mensagem padrão se não fornecida
      code: errorData.code || 500,                     // código de erro padrão
      timestamp: new Date().toISOString(),             // hora em que ocorreu o erro
      customDescription: errorData.customDescription || '', // descrição extra opcional
    });
  };

  // Função para limpar o erro
  const clearError = () => {
    setError(null);
  };

  // fornece o estado do erro e as funções para todos os componentes filhos
  return (
    <ErrorContext.Provider value={{ error, showError, clearError }}>
      {children}
    </ErrorContext.Provider>
  );
};

// Hook personalizado para consumir o contexto de erro
export const useError = () => {
  const context = useContext(ErrorContext);
  if (!context) {
    // faz com que só seja usado dentro do ErrorProvider
    throw new Error('useError deve ser usado dentro do ErrorProvider');
  }
  return context; // Retorna context
};
