import React, { useState, useEffect } from 'react';
import { Layout, Button, theme, ConfigProvider } from 'antd';
import { MenuUnfoldOutlined, MenuFoldOutlined } from '@ant-design/icons';
import MenuList from "../components/MenuList";
import ToggleThemeButton from "../components/ToggleThemeButton";
import { FaRegBell, FaRegUser } from "react-icons/fa";
import { MdArrowOutward, MdOutlineCalendarToday, MdOutlineFileDownload } from "react-icons/md";
import { GoMortarBoard } from "react-icons/go";
import { IoBagOutline } from "react-icons/io5";
import { LuFilter } from "react-icons/lu";
import { FiUserPlus } from "react-icons/fi";
import "../styles/page/index.css"



const { Header, Sider } = Layout;

function App() {
  const [darkTheme, setDarkTheme] = useState(false);
  const [collapsed, setCollapsed] = useState(false);

  const toggleTheme = () => {
    setDarkTheme(!darkTheme);
  };

  useEffect(() => {
    document.body.className = darkTheme ? "dark" : "light";
  }, [darkTheme]);

  const {
    token: { colorBgContainer },
  } = theme.useToken();

  return (
    <ConfigProvider theme={{ algorithm: darkTheme ? theme.darkAlgorithm : theme.defaultAlgorithm }}>
      <Layout>
        <Sider
          collapsed={collapsed}
          collapsible
          trigger={null}
          theme={darkTheme ? 'dark' : 'light'}
          className='sidebar'
        >
          <MenuList darkTheme={darkTheme} />
          <ToggleThemeButton darkTheme={darkTheme} toggleTheme={toggleTheme} />
        </Sider>
        <Layout>
          <Header
            style={{
              padding: 0,
              background: darkTheme ? 'rgb(0, 0, 0)' : colorBgContainer,

            }}
          >
            <Button
              type='text'
              className='sino'
              icon={<FaRegBell />}
            />

            <Button
              type='text'
              className='toggle'
              onClick={() => setCollapsed(!collapsed)}
              icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
            />
          </Header>
          <Layout>
            <Button className='div1' color='blue' style={{ background: darkTheme ? "rgb(255, 0, 0)" : "rgb(17, 255, 0)" }}>
              <Button className='boneco' color='blue' icon={<FaRegUser />}>
                <Button className='seta' color='blue' icon={<MdArrowOutward />}></Button>


              </Button>
            </Button>
            <Button className='div2' color='blue'>
              <Button className='seta2' color='blue' icon={<MdArrowOutward />}></Button>
              <Button className='estudante' color='blue' icon={<GoMortarBoard />}></Button>

            </Button>
            <Button className='div3' color='blue'>
              <Button className='bolsa' color='blue' icon={<IoBagOutline />}>

              </Button>
              <Button className='seta3' color='blue' icon={<MdArrowOutward />}></Button>

            </Button>
            <Button className='div4' color='blue'></Button>
            <Button className='div5' color='blue'></Button>
            <Button className='div6' color='blue'></Button>
            <Button className='all' color='blue'>
              <p>All</p>

            </Button>
            <Button className='engagement' color='blue'>
              <p>Engagement</p>
            </Button>
            <Button className='visit' color='blue'>
              <p>Visit</p>
            </Button>
            <Button className='post' color='blue'>
              <p>Post</p>
            </Button>
            <Button className='filtro' color='blue' icon={<LuFilter />}></Button>
            <Button className='calendario' color='blue' icon={<MdOutlineCalendarToday />}></Button>
            <Button className='DownloadPDF' color='blue' icon={<MdOutlineFileDownload />}>
              <p>Download PDF</p></Button>

          </Layout>
        </Layout>
      </Layout>
    </ConfigProvider>
  );
}

export default App;
