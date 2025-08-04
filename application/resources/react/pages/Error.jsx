import { useNavigate, useLocation } from 'react-router-dom';
import TopBar from '../components/TopBar';
import '../styles/page/Error.css';

export default function ErrorPage() {
    const navigate = useNavigate();
    const location = useLocation();

    const { code, message, customDescription } = location.state || {
        code: 500,
        message: 'Erro desconhecido',
        customDescription: 'Ocorreu um erro inesperado. Tente novamente mais tarde.'
    };

    const handleIconClick = () => {
        const errorIcon = document.querySelector('.error-icon');
        if (errorIcon) {
            errorIcon.style.transform = 'scale(1.2) rotate(360deg)';
            setTimeout(() => {
                errorIcon.style.transform = 'scale(1) rotate(0deg)';
            }, 500);
        }
    };

    return (
        <div className="containerE">
            <TopBar />

            <div className="content">
                <div className="error-content">
                    <div
                        className="error-icon"
                        onClick={handleIconClick}
                        style={{ cursor: 'pointer', transition: 'all 0.5s ease' }}
                    >
                        🚀
                    </div>
                    <h1 className="error-code">{code}</h1>
                    <h2 className="error-title">{message}</h2>
                    <p className="error-description">{customDescription}</p>

                    <button className="btn-primary" onClick={() => navigate(-1)}>
                        🏠 Voltar
                    </button>
                </div>
            </div>
        </div>
    );
}
