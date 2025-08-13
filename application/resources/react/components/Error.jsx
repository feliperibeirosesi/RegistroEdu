import { useNavigate, useLocation } from 'react-router-dom';
// useNavigate → Hook do React Router para navegar entre rotas
// useLocation -> Hook para acessar informações sobre a rota atual

import TopBar from './TopBar';
// Componente que renderiza a barra superior

import '../styles/page/Error.css';
// Importar o CSS

export default function ErrorPage({ error }) {
    // Componente que recebe "error"

    const navigate = useNavigate(); // Permite mudar de página por código
    const location = useLocation(); // Obtém dados sobre a navegação atual

    // Desestruturação dos dados do erro:
    const { code, message, customDescription } = error || location.state || {
        code: 500,
        message: 'Erro desconhecido',
        customDescription: 'Ocorreu um erro inesperado. Tente novamente mais tarde.'
    };

    const handleIconClick = () => {
        // Função chamada ao clicar no ícone
        // Aplica uma transformação CSS para animar
        const errorIcon = document.querySelector('.error-icon');
        if (errorIcon) {
            errorIcon.style.transform = 'scale(1.2) rotate(360deg)';
            setTimeout(() => {
                errorIcon.style.transform = 'scale(1) rotate(0deg)';
            }, 500); // Após 0,5s, volta a escala original
        }
    };

    return (
        <div className="containerE">
            {/* Container geral da página de erro */}
            <TopBar /> {/* Componente fixo no topo */}

            <div className="content">
                <div className="error-content">
                    //
                    {/* Ícone de erro interativo */}
                    <div
                        className="error-icon"
                        onClick={handleIconClick}
                        style={{ cursor: 'pointer', transition: 'all 0.5s ease' }}
                    >
                        🚀
                    </div>

                    {/* Código do erro (ex: 404) */}
                    <h1 className="error-code">{code}</h1>

                    {/* Mensagem principal do erro */}
                    <h2 className="error-title">{message}</h2>

                    {/* Descrição adicional do erro */}
                    <p className="error-description">{customDescription}</p>

                    {/* Botão para voltar para a página anterior */}
                    <button className="btn-primary" onClick={() => navigate(-1)}>
                        🏠 Voltar
                    </button>
                </div>
            </div>
        </div>
    );
}
