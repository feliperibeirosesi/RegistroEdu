import ProfileMenu from './ProfileMenu';
import '../styles/components/TopBar.css';

export default function TopBar() {
    return (
        <div className="top-bar">
            <div className="logo">RegistroEdu</div>
            <ProfileMenu />
        </div>
    );
}
