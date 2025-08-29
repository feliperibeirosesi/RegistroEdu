import { useState } from 'react';
import { Routes, Route } from 'react-router-dom'; ' ,'
import { Button, Layout, theme } from 'antd'
import { MenuUnfoldOutlined, MenuFoldOutlined } from '@ant-design/icons'
import ToggleThemeButton from './components/ToggleThemeButton'
import MenuList from './components/MenuList'

import Home from './pages/Home';
import SingIn from './pages/SingIn';
import Teste from './pages/teste';
import LoggedIn from './pages/LoggedIn'
const { Header, Sider } = Layout

function App() {
    const [darkTheme, setDarkTheme] = useState(true)
    const [collapsed, setCollapsed] = useState(true)

    const toggleTheme = () => {
        setDarkTheme(!darkTheme)
    }

    const { token: { colorBgContainer },
    } = theme.useToken();


    return (
        <>
            <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/singin" element={<SingIn />} />
                <Route path="/teste" element={<Teste />} />
                <Route path="/loggedin" element={<LoggedIn />} />
            </Routes>


        </>
    );
}

export default App;
