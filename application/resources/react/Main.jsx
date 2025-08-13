import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';

import { ErrorProvider, useError } from './contexts/ErrorContext';
import App from './App';
import ErrorPage from './components/Error';

import { StrictMode } from 'react'
import './styles/page/index.css'

function AppContent() {
    const { error } = useError();

    if (error) {
        return <ErrorPage error={error} />;
    }

    return <App />;
}

function Main() {
    return (
        <ErrorProvider>
            <BrowserRouter>
                <AppContent />
            </BrowserRouter>
        </ErrorProvider>
    );
}

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

createRoot(document.getElementById('root')).render(
    <StrictMode>
        <App />
    </StrictMode>,
)


export default Main;




