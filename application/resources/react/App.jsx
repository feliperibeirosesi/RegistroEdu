import { Routes, Route } from 'react-router-dom';
import ErrorBoundary from './components/ErrorBoundary';

import Home from './pages/Home';
import SingIn from './pages/SingIn';
import Teste from './pages/teste';

function App() {
    return (
        <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/singin" element={<SingIn />} />
            <Route path="/teste" element={<Teste />} />
        </Routes>
    );
}

export default App;
