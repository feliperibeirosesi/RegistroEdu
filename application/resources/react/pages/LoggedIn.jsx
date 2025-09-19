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

            <div className='div0'>
              <Button className='profile'></Button>
            </div>

            <div className='div1'>
              <div className='boneco' ><div className='Icons1'>
                <FaRegUser /></div></div>
              <Button className='seta' icon={<MdArrowOutward />}></Button>


            </div>
            <div className='div2'>
              <Button className='seta2' icon={<MdArrowOutward />}></Button>
              <Button className='estudante' icon={<GoMortarBoard />}></Button>

            </div>
            <div className='div3'>
              <Button className='bolsa' icon={<IoBagOutline />}>

              </Button>
              <Button className='seta3' icon={<MdArrowOutward />}></Button>

            </div>
            <Button className='btn1' ></Button>
            <Button className='btn2' ></Button>
            <Button className='btn3' ></Button>
            <Button className='all' >
              <p>All</p>

            </Button>
            <Button className='engagement' >
              <p>Engagement</p>
            </Button>
            <Button className='visit' >
              <p>Visit</p>
            </Button>
            <Button className='post' >
              <p>Post</p>
            </Button>
            <Button className='filtro' icon={<LuFilter />}></Button>
            <Button className='calendario' icon={<MdOutlineCalendarToday />}></Button>
            <Button className='DownloadPDF' icon={<MdOutlineFileDownload />}>
              <p>Download PDF</p></Button>
          </Layout>
        </Layout>
      </Layout >
    </ConfigProvider >
  );
}

export default App;
