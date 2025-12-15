import React from "react";
import { Navigate } from "react-router-dom";
import { useAuth } from "../../hooks/useAuth";
import { jwtDecode } from "jwt-decode";

function ProtectedRoute({ element }) {
  const { isAuthenticated } = useAuth();

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  // Obtener token
  const token = localStorage.getItem("accessToken");

  if (!token) {
    return <Navigate to="/login" replace />;
  }

  try {
    const decoded = jwtDecode(token);
    const currentTime = Date.now() / 1000;

    // ✔ Si el token expiró → expulsar sesión
    if (decoded.exp < currentTime) {
      localStorage.setItem("sessionExpired", "true");
      localStorage.removeItem("accessToken");
      localStorage.removeItem("refreshToken");
      localStorage.removeItem("user");
      return <Navigate to="/login" replace />;
    }

  } catch (err) {
    // Si el token es inválido → logout
    return <Navigate to="/login" replace />;
  }

  return element;
}

export default ProtectedRoute;
