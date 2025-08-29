import React from "react";
import { HiOutlineSun, HiOutlineMoon } from 'react-icons/hi'
import { Button } from "antd";

const ToggleThemeButton = ({ darkTheme, toggleTheme }) => {
  return (<div className="toggle-theme-btn">
    <Button style={{ background: darkTheme ? "rgb(0, 0, 0)" : "rgb(255, 255, 255)", color: darkTheme ? "rgb(255, 255, 255)" : "rgb(0, 0, 0)" }} onClick={toggleTheme}>
      {darkTheme ? <HiOutlineSun /> : <HiOutlineMoon />}
    </Button>
  </div >
  )
}

export default ToggleThemeButton
