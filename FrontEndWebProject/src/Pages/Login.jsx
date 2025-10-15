import api from "../API/axios"
import { useState } from "react"
import { useNavigate } from "react-router-dom"

export default function Login() {
  const [email, setEmail] = useState("");
  const [senha, setSenha] = useState("");
  const navigate = useNavigate();

  async function handleLogin(e) {
    e.preventDefault();

    try {
      const res = await api.put("/auth/login", { email, senha });
      localStorage.setItem("token", res.data.token);
      navigate("/profile")

    } catch (error) {
      alert("Erro ao efetuar login ", error)
    }

  }

  return (
    <div>
      <h2>Login</h2>
      <form onSubmit={handleLogin}>
        <input placeholder="Email" type="text" value={email} onChange={(e) => setEmail(e.target.value)} />
        <input placeholder="Senha" type="text" value={senha} onChange={(e) => setSenha(e.target.value)} />
        <button type="submit">Login</button>
      </form>
    </div>
  )
}

