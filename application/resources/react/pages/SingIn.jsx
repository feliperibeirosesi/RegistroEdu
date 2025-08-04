import TopBar from '../components/TopBar';
import { useNavigate } from 'react-router-dom';
import '../styles/page/register.css';

const Register = () => {
    const navigate = useNavigate();
    const isLoggedIn = false;

    return (
        <div className="container">
            <TopBar />

            <div className="forms">
                <h1>Entrar</h1>

                <p className="authorization-warning">
                    Atenção: o acesso ao site será permitido somente após autorização de um administrador.
                </p>
                <a href="/auth/google" className="google-btn" id="google-btn">
                    <img src="./assets/register/google-icon.png" alt="Google" />
                    Entrar com Google
                </a>
            </div>
        </div>
    );
};

export default Register;
