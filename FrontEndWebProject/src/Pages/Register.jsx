import api from "../API/axios"
import { useState } from "react"
import { useNavigate } from "react-router-dom"

export default function Register() {
  const [nome, setNome] = useState("");
  const [email, setEmail] = useState("");
  const [senha, setSenha] = useState("");
  const navigate = useNavigate();

  async function handleRegister(e) {
    e.preventDefault();

    try {
      const res = await api.put("/auth/login", { nome, email, senha });
      alert(res.configdata.message)
      navigate("/profile")

    } catch (error) {
      alert("Erro ao efetuar login ", error)
    }

  }

  return (
    <div>
      <h2>Registrar</h2>
      <form onSubmit={handleRegister}>
        <input placeholder="Nome" type="text" value={nome} onChange={(e) => setNome(e.target.value)} />
        <input placeholder="Email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
        <input placeholder="Senha" type="text" value={senha} onChange={(e) => setSenha(e.target.value)} />
        <button type="submit">Login</button>
      </form>
    </div>
  )
}

