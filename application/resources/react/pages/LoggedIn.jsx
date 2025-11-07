// Importa o React e os hooks useState e useEffect
import React, { useState, useEffect } from 'react';

// Importa componentes do Ant Design (Layout, Botão, tema e provedor de configuração)
import { Layout, Button, theme, ConfigProvider } from 'antd';

// Importa ícones do Ant Design
import { MenuUnfoldOutlined, MenuFoldOutlined } from '@ant-design/icons';

// Importa componentes criados pelo desenvolvedor
import MenuList from "../components/MenuList";
import ToggleThemeButton from "../components/ToggleThemeButton";

// Importa vários ícones de diferentes bibliotecas (react-icons)
import { FaRegBell, FaRegUser } from "react-icons/fa";
import { MdArrowOutward, MdOutlineCalendarToday, MdOutlineFileDownload } from "react-icons/md";
import { GoMortarBoard } from "react-icons/go";
import { IoBagOutline } from "react-icons/io5";
import { LuFilter } from "react-icons/lu";
import { FiUserPlus } from "react-icons/fi";

// Importa o CSS da página e outros componentes
import "../styles/page/index.css"
import Graphic from '../components/Graphic';

// Importa o componente de gráfico de barras do Chart.js
import { Bar } from 'react-chartjs-2';


// Desestrutura os componentes Header e Sider do Layout do Ant Design
const { Header, Sider } = Layout;


// Componente principal do aplicativo
function App() {

  // Estado que controla se o tema é escuro (true) ou claro (false)
  const [darkTheme, setDarkTheme] = useState(false);

  // Estado que controla se a barra lateral (menu) está recolhida (true) ou aberta (false)
  const [collapsed, setCollapsed] = useState(false);

  // Função que alterna o tema (claro ↔ escuro)
  const toggleTheme = () => {
    setDarkTheme(!darkTheme);
  };

  // Efeito que muda a classe do body sempre que o tema é alterado
  useEffect(() => {
    document.body.className = darkTheme ? "dark" : "light";
  }, [darkTheme]);

  // Pega a cor de fundo padrão do tema atual do Ant Design
  const {
    token: { colorBgContainer },
  } = theme.useToken();


  // Retorna a estrutura visual da página
  return (
    // ConfigProvider aplica o tema globalmente (claro ou escuro)
    <ConfigProvider theme={{ algorithm: darkTheme ? theme.darkAlgorithm : theme.defaultAlgorithm }}>

      <Layout>
        {/* Barra lateral (menu) */}
        <Sider
          collapsed={collapsed}           // define se está recolhido
          collapsible                     // permite recolher/expandir
          trigger={null}                  // remove o botão padrão de recolher
          theme={darkTheme ? 'dark' : 'light'}  // muda o tema visual conforme o estado
          className='sidebar'             // classe CSS para estilos personalizados
        >
          {/* Componente de menu personalizado */}
          <MenuList darkTheme={darkTheme} />

          {/* Botão para alternar entre tema claro e escuro */}
          <ToggleThemeButton darkTheme={darkTheme} toggleTheme={toggleTheme} />
        </Sider>

        {/* Área principal do layout */}
        <Layout>
          {/* Cabeçalho superior */}
          <Header
            style={{
              padding: 0,
              background: darkTheme ? 'rgb(0, 0, 0)' : colorBgContainer, // muda cor conforme tema
            }}
          >
            {/* Ícone de sino (notificações) */}
            <Button
              type='text'
              className='sino'
              icon={<FaRegBell />}
            />

            {/* Botão que abre/fecha o menu lateral */}
            <Button
              type='text'
              className='toggle'
              onClick={() => setCollapsed(!collapsed)}  // alterna o estado collapsed
              icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />} // muda o ícone conforme o estado
            />
          </Header>

          {/* Conteúdo abaixo do header */}
          <Layout>

            {/* Div para o botão de perfil (ainda vazio) */}
            <div className='div0'>
              <Button className='profile'></Button>
            </div>

            {/* Bloco 1: Ícone de usuário e seta */}
            <div className='div1'>
              <div className='boneco'>
                <div className='Icons1'>
                  <FaRegUser /> {/* Ícone de usuário */}
                </div>
              </div>
              <Button className='seta' icon={<MdArrowOutward />}></Button> {/* Seta apontando para fora */}
            </div>

            {/* Bloco 2: Ícone de estudante e seta */}
            <div className='div2'>
              <Button className='seta2' icon={<MdArrowOutward />}></Button>
              <Button className='estudante' icon={<GoMortarBoard />}></Button>
            </div>

            {/* Bloco 3: Ícone de bolsa e seta */}
            <div className='div3'>
              <Button className='bolsa' icon={<IoBagOutline />}></Button>
              <Button className='seta3' icon={<MdArrowOutward />}></Button>
            </div>

            {/* Botões visuais sem ícones (talvez usados para layout ou gráficos) */}
            <Button className='btn1'>
              <Graphic></Graphic>
            </Button>
            <Button className='btn2'></Button>
            <Button className='btn3'></Button>

            {/* Botões de filtro (provavelmente categorias ou abas) */}
            <Button className='all'><p>All</p></Button>
            <Button className='engagement'><p>Engagement</p></Button>
            <Button className='visit'><p>Visit</p></Button>
            <Button className='post'><p>Post</p></Button>

            {/* Botões de ação adicionais */}
            <Button className='filtro' icon={<LuFilter />}></Button> {/* Botão de filtro */}
            <Button className='calendario' icon={<MdOutlineCalendarToday />}></Button> {/* Botão de calendário */}

            {/* Botão para baixar PDF */}
            <Button className='DownloadPDF' icon={<MdOutlineFileDownload />}>
              <p>Download PDF</p>
            </Button>
          </Layout>
        </Layout>
      </Layout>
    </ConfigProvider>
  );
}

// Exporta o componente principal
export default App;
