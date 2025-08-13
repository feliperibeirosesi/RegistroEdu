import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';

import { ErrorProvider, useError } from './contexts/ErrorContext';
import App from './App';
import ErrorPage from './components/Error'; // página de erro personalizada

//decide se mostra a aplicação ou a página de erro
function AppContent() {
    const { error } = useError();

    if (error) {
        return <ErrorPage error={error} />;
    }

    return <App />;
}

//envolve toda a aplicação com provedores e roteamento
function Main() {
    return (
        <ErrorProvider>
            <BrowserRouter>
                <AppContent />
            </BrowserRouter>
        </ErrorProvider>
    );
}

// Aguarda o carregamento do DOM antes de montar o React
document.addEventListener('DOMContentLoaded', () => {
    const mountEl = document.getElementById('root');
    if (mountEl) {
        const root = createRoot(mountEl);
        root.render(<Main />);
    } else {
        console.error('Elemento #root não encontrado no DOM.');
    }

    const preloader = document.getElementById('preloader');
    if (preloader) preloader.remove();
});

export default Main;
