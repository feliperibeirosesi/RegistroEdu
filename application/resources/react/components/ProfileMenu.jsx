import { useState } from 'react';
import '../styles/components/ProfileMenu.css';

export default function ProfileMenu() {
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
