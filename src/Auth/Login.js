import React, { useState } from "react";
import { useEffect } from "react";
import { Container, Row, Col, Form, Button } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../../hooks/useAuth";
import Particle from "../Particle";
import "./Auth.css";
import { FaEnvelope, FaLock } from "react-icons/fa";

function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const navigate = useNavigate();
  const { login } = useAuth();

  useEffect(() => {
    const expired = localStorage.getItem("sessionExpired");
    if (expired) {
      setError("⏳ Tu sesión ha expirado por seguridad. Por favor inicia sesión nuevamente.");
      localStorage.removeItem("sessionExpired");
    }
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    try {
      await login(email, password);
      navigate("/");
    } catch (err) {
      setError(err.response?.data?.msg || "Error al iniciar sesión. Intenta de nuevo.");
    } finally {
      setLoading(false);
    }
  };

  
  return (
    <section>
      <Container fluid className="auth-section" id="login">
        <Particle />
        <Container className="auth-content">
          <Row className="align-items-center" style={{ minHeight: "100vh" }}>
            <Col md={6} lg={5} className="mx-auto">
              <div className="auth-card">
                <h1 className="auth-heading">Bienvenido de vuelta</h1>
                <p className="auth-subheading">Inicia sesión en tu cuenta</p>

                {error && (
                  <div className="alert alert-danger" role="alert">
                    {error}
                  </div>
                )}

                <Form onSubmit={handleSubmit} className="auth-form">
                  {/* Email Input */}
                  <Form.Group className="mb-4">
                    <div className="input-wrapper">
                      <FaEnvelope className="input-icon" />
                      <Form.Control
                        type="email"
                        placeholder="Correo electrónico"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                        className="auth-input"
                      />
                    </div>
                  </Form.Group>

                  {/* Password Input */}
                  <Form.Group className="mb-4">
                    <div className="input-wrapper">
                      <FaLock className="input-icon" />
                      <Form.Control
                        type={showPassword ? "text" : "password"}
                        placeholder="Contraseña"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                        className="auth-input"
                      />
                      <button
                        type="button"
                        className="show-password-btn"
                        onClick={() => setShowPassword(!showPassword)}
                      >
                        {showPassword ? "👁️" : "👁️‍🗨️"}
                      </button>
                    </div>
                  </Form.Group>

                  {/* Submit Button */}
                  <Button
                    variant="primary"
                    type="submit"
                    className="auth-btn w-100"
                    disabled={loading}
                  >
                    {loading ? "Cargando..." : "Iniciar sesión"}
                  </Button>
                </Form>

                {/* Links */}
                <div className="auth-links">
                  <p>
                    ¿No tienes cuenta?{" "}
                    <a href="/signup" className="auth-link">
                      Regístrate aquí
                    </a>
                  </p>
                  <p>
                    <a href="/#" className="auth-link">
                      ¿Olvidaste tu contraseña?
                    </a>
                  </p>
                </div>
              </div>
            </Col>
          </Row>
        </Container>
      </Container>
    </section>
  );
}

export default Login;
