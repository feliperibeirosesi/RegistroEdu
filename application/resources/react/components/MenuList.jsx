import { Menu } from "antd";
import { HomeOutlined, CalendarOutlined, BookOutlined, SettingOutlined, WarningOutlined } from '@ant-design/icons'
import { IoFolderOutline } from "react-icons/io5";
import { LuTimerOff } from "react-icons/lu";
import { GrClose } from "react-icons/gr";

const MenuList = ({ darkTheme }) => {
  return (
    <Menu theme={darkTheme ? 'dark' : 'light'} mode="inline" className="menu-bar">
      <Menu.Item key="home" icon={<HomeOutlined />}>Home</Menu.Item>
      <Menu.SubMenu key="Documentos" icon={<IoFolderOutline />} title="Documentação">
        <Menu.Item key="Ocorrências" icon={<LuTimerOff />}>Ocorrências</Menu.Item>
        <Menu.Item key="Planon" icon={<WarningOutlined />}>Planon</Menu.Item></Menu.SubMenu>
      <Menu.Item key="atividades" icon={<BookOutlined />}>Atividades</Menu.Item>
      <Menu.Item key="calendario" icon={<CalendarOutlined />}>Calendário</Menu.Item>
      <Menu.Item key="configuracoes" icon={<SettingOutlined />}>Configurações</Menu.Item>
    </Menu>
  )
}

export default MenuList
