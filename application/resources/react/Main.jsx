import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';

function Main() {
    return (
        <BrowserRouter>
            <App />
        </BrowserRouter>
    );
}

document.addEventListener('DOMContentLoaded', () => {
    const mountEl = document.getElementById('root');
    if (mountEl) {
        const root = createRoot(mountEl);
        root.render(<Main />);

        const preloader = document.getElementById('preloader');
        if (preloader) preloader.remove();
    }
});
