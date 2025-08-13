import ProfileMenu from './ProfileMenu';
// importa o componente de menu de perfil

import '../styles/components/TopBar.css';
// importa os estilos específicos da TopBar

export default function TopBar() {
    return (
        <div className="top-bar">
            <div className="logo">RegistroEdu</div>
            <ProfileMenu />
        </div>
    );
}
