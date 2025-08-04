import { Routes, Route } from 'react-router-dom';
import Home from './pages/Home';
import SingIn from './pages/SingIn';

export default function App() {
    return (
        <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/singin" element={<SingIn />} />
        </Routes>
    );
}
