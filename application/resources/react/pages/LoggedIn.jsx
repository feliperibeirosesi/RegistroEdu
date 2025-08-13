import MenuList from "../components/MenuList";
import ToggleThemeButton from "../components/ToggleThemeButton";


const { Header, Sider } = Layout

function App() {


  const toggleTheme = () => {
    setDarkTheme(!darkTheme)
  }

  const { token: { colorBgContainer },
  } = theme.useToken();
  return (
    <>
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

  )
}

export default App
