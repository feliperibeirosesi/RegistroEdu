import { useState } from 'react';
import '../styles/components/ProfileMenu.css';
// importa os estilos do menu perfil

export default function ProfileMenu() {
    // estado que controla se o menu está aberto ou fechado
    const [open, setOpen] = useState(false);

    return (
        <>
            <button className="profile-icon" onClick={() => setOpen(!open)}>
                <i className="fa-solid fa-circle-user"></i>
            </button>

            <div className={`profile-menu${open ? ' show' : ''}`}>
                <a href="/">Home</a>
            </div>
        </>
    );
}
