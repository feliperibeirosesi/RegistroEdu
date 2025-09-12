import { useNavigate, useLocation } from 'react-router-dom';
import TopBar from '../components/TopBar';
import '../styles/page/Error.css';

export default function ErrorPage() {

    // Hook do React Router para navegar entre rotas
    const navigate = useNavigate();

    // Hook que recupera informações da rota atual
    const location = useLocation();

    // Recupera os dados de erro vindos da navegação
    // Se não existirem, usa valores padrão
    const { code, message, customDescription } = location.state || {
        code: 500,
        message: 'Erro desconhecido',
        customDescription: 'Ocorreu um erro inesperado. Tente novamente mais tarde.'
    };

    // Função para animar o ícone de erro ao clicar
    const handleIconClick = () => {
        const errorIcon = document.querySelector('.error-icon');
        if (errorIcon) {
            errorIcon.style.transform = 'scale(1.2) rotate(360deg)';
            setTimeout(() => {
                errorIcon.style.transform = 'scale(1) rotate(0deg)';
            }, 500); // Volta ao normal após 0.5s
        }
    };

    return (
        <div className="containerE">
            {/* Barra de navegação no topo */}
            <TopBar />

            {/* Conteúdo central da página de erro */}
            <div className="content">
                <div className="error-content">

                    <div
                        className="error-icon"
                        onClick={handleIconClick}
                        style={{ cursor: 'pointer', transition: 'all 0.5s ease' }}
                    >
                        🚀
                    </div>

                    {/* Exibição do código e mensagem de erro */}
                    <h1 className="error-code">{code}</h1>
                    <h2 className="error-title">{message}</h2>
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
