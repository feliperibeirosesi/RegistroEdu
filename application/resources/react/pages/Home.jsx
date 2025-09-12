import { useNavigate } from 'react-router-dom';
import TopBar from '../components/TopBar';
import '../styles/page/Home.css';

export default function Home() {
    // Hook do React Router para navegação entre rotas
    const navigate = useNavigate();

    // Estado simples para simular se o usuário está logado ou não
    const isLoggedIn = false;

    return (
        <div className="containerH">
            {/* Barra de navegação no topo */}
            <TopBar />

            {/* Conteúdo principal da página inicial */}
            <div className="content">
                <div className="welcome-box">
                    <h1>Bem Vindo!</h1>
                    <h2>Sistema de Registro Educacional</h2>
                    <p>
                        Plataforma moderna para gestão de documentos educacionais e registros acadêmicos.
                    </p>

                    {/* Seção de funcionalidades com ícones */}
                    <div className="features">
                        <div className="feature-item">
                            <i className="fas fa-file-alt"></i>
                            <span>Documentos Digitais</span>
                        </div>
                        <div className="feature-item">
                            <i className="fas fa-shield-alt"></i>
                            <span>Segurança Garantida</span>
                        </div>
                        <div className="feature-item">
                            <i className="fas fa-users"></i>
                            <span>Acesso Controlado</span>
                        </div>
                    </div>

                    {/* Botões principais - mudam de acordo com login */}
                    <div className="cta-buttons">
                        {!isLoggedIn ? (

                            // Caso o usuário não esteja logado
                            <>
                                <button
                                    className="btn btn-primary"
                                    onClick={() => navigate('/singin')}
                                >
                                    <i className="fas fa-sign-in-alt"></i> Fazer Login
                                </button>

                                <button
                                    className="btn btn-secondary"
                                    onClick={() =>
                                        document.getElementById('info-section').scrollIntoView({ behavior: 'smooth' })
                                    }
                                >
                                    <i className="fas fa-info-circle"></i> Saiba Mais
                                </button>
                            </>
                        ) : (

                            // Caso o usuário já esteja logado
                            <>
                                <button
                                    className="btn btn-primary"
                                    onClick={() => navigate('/documentos')}
                                >
                                    <i className="fas fa-folder-open"></i> Acessar Documentos
                                </button>

                                <button
                                    className="btn btn-secondary"
                                    onClick={() => navigate('/perfil')}
                                >
                                    <i className="fas fa-user"></i> Meu Perfil
                                </button>
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/* Seção "Saiba Mais" com passo a passo */}
            <div className="info-section" id="info-section">
                <div className="info-container">
                    <h3>Como Funciona?</h3>
                    <div className="steps">
                        {/* Passo 1 */}
                        <div className="step">
                            <div className="step-number">1</div>
                            <div className="step-content">
                                <h4>Cadastro</h4>
                                <p>Faça login com sua conta Google institucional</p>
                            </div>
                        </div>
                        {/* Passo 2 */}
                        <div className="step">
                            <div className="step-number">2</div>
                            <div className="step-content">
                                <h4>Autorização</h4>
                                <p>Aguarde aprovação de um administrador</p>
                            </div>
                        </div>
                        {/* Passo 3 */}
                        <div className="step">
                            <div className="step-number">3</div>
                            <div className="step-content">
                                <h4>Acesso Completo</h4>
                                <p>Gerencie documentos e registros educacionais</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
