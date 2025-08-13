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

            <Layout>
                <Sider collapsed={collapsed} collapsible trigger={null} theme={darkTheme ? 'dark' : 'light'} className='sidebar'>

                    <MenuList darkTheme={darkTheme} />
                    <ToggleThemeButton darkTheme={darkTheme} toggleTheme={toggleTheme} />
                </Sider>
                <Layout>
                    <Header style={{ padding: 0, background: darkTheme ? 'rgb(0, 21, 41)' : colorBgContainer, transition: "all 0.5s ease" }}>
                        <Button style={{ color: darkTheme ? "rgb(255, 255, 255)" : "rgb(0, 0, 0)" }} type='text' className='toggle' onClick={() => setCollapsed(!collapsed)} icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />} />
                    </Header>
                </Layout>
            </Layout>
        </>
    );
}

export default App;
