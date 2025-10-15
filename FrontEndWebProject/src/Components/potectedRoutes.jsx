import { Navigate } from "react-router-dom";

export default function protectedRoute({ children }) {
  const token = localStorage.getItem("token");

  if (!token) {
    return <navigate to="/login" />
  }

  return children
}
