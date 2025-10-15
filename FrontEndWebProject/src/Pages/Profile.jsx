import api from "../API/axios"
import { useState, useEffect } from "react"
import { useNavigate } from "react-router-dom"

export default function Profile() {
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    async function fetchUser() {

      try {
        const res = await api.get("/auth/profile");
        setUser(res.data.user);
      } catch (error) {
        alert("Error ao carregar perfil!", error);
        navigate("/Login")
      }

    }

    fetchUser();
  }, [])

  async function handleDelete(e) {
    if (window.confirm("Tem certeza que deseja deletar sua conta?")) {
      await api.delete("/auth/delete");
      localStorage.removeItem("token");
      navigate("/Register")
    }
  }
  return (
    <div>
      <h2>Perfil</h2>
      {user ? (
        <>
          <p><strong>Nome:</strong> {user.none}</p>
          <p><strong>Email:</strong> {user.none}</p>
          <button onClick={() => navigate("editProfile")}>Editar Perfil</button>
          <button onClick={handleDelete}>Excluir Conta</button>
        </>
      ) : (
        <p>Carregando...</p>
      )}
    </div>
  )
}
