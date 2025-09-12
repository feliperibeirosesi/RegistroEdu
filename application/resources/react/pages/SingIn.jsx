import TopBar from '../components/TopBar';
import { useNavigate } from 'react-router-dom';
import '../styles/page/register.css';

const Register = () => {
    // Hook do React Router para redirecionar o usuário
    const navigate = useNavigate();

    // Estado fictício para verificar se o usuário está logado
    const isLoggedIn = false;

    return (
        <div className="container">
            {/* Barra de navegação no topo */}
            <TopBar />

            {/* Área central com o formulário de login */}
            <div className="forms">
                <h1>Entrar</h1>

                {/* Aviso importante sobre a autorização */}
                <p className="authorization-warning">
                    Atenção: o acesso ao site será permitido somente após autorização de um administrador.
                </p>

                {/* Botão de login via Google */}
                <a href="/auth/google" className="google-btn" id="google-btn">
                    <img src="./assets/register/google-icon.png" alt="Google" />
                    Entrar com Google
                </a>
            </div>
        </div>
    );
};

export default Register;
