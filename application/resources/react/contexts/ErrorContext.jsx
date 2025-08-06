import React, { createContext, useContext, useState } from 'react';

const ErrorContext = createContext();

export const ErrorProvider = ({ children }) => {
  const [error, setError] = useState(null);

  const showError = (errorData) => {
    setError({
      message: errorData.message || 'Algo deu errado',
      code: errorData.code || 500,
      timestamp: new Date().toISOString(),
      customDescription: errorData.customDescription || '',
    });
  };

  const clearError = () => {
    setError(null);
  };

  return (
    <ErrorContext.Provider value={{ error, showError, clearError }}>
      {children}
    </ErrorContext.Provider>
  );
};

export const useError = () => {
  const context = useContext(ErrorContext);
  if (!context) {
    throw new Error('useError deve ser usado dentro do ErrorProvider');
  }
  return context;
};
