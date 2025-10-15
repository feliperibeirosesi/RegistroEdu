import { BrowserRouter, Route, Routes, Navigate } from "react-router-dom";
import Login from "./Pages/Login";
import Register from "./Pages/Register";
import Profile from "./Pages/Profile";
import EditProfile from "./Pages/editProfile";
import ProtectedRoute from "./Components/potectedRoutes";

function App() {

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={< Login />} />
        <Route path="/register" element={< Register />} />
        <Route path="/profile" element={<ProtectedRoute > < Profile /> </ ProtectedRoute >} />
        <Route path="/editProfile" element={<ProtectedRoute > < EditProfile /> </ ProtectedRoute>} />
      </Routes>
    </BrowserRouter>
  )
}

<div>
  <h1></h1>
</div>

export default App
