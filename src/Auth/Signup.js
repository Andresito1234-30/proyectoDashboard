import React, { useState } from "react";
import { Container, Row, Col, Form, Button } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../../hooks/useAuth";
import { authAPI } from "../../services/api";
import Particle from "../Particle";
import "./Auth.css";
import { FaUser, FaEnvelope, FaLock } from "react-icons/fa";

function Signup() {
  const [formData, setFormData] = useState({
    firstname: "",
    lastname: "",
    email: "",
    password: "",
    confirmPassword: "",
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const navigate = useNavigate();
  const { login } = useAuth();

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    // Validaciones básicas
    if (formData.password !== formData.confirmPassword) {
      setError("Las contraseñas no coinciden");
      return;
    }

    if (formData.password.length < 6) {
      setError("La contraseña debe tener al menos 6 caracteres");
      return;
    }

    setLoading(true);

    try {
      // Registrar el usuario
      await authAPI.register({
        firstname: formData.firstname,
        lastname: formData.lastname,
        email: formData.email,
        password: formData.password,
      });

      setSuccess("¡Cuenta creada exitosamente! Iniciando sesión...");

      // Esperar 1.5 segundos y luego hacer login automático
      setTimeout(async () => {
        try {
          await login(formData.email, formData.password);
          navigate("/");
        } catch (err) {
          // Si el login automático falla, redirigir al login manual
          setError("Cuenta creada. Por favor, inicia sesión manualmente.");
          setTimeout(() => navigate("/login"), 2000);
        }
      }, 1500);
    } catch (err) {
      setError(
        err.response?.data?.message ||
          err.response?.data?.msg ||
          "Error al registrarse. Intenta de nuevo."
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <section>
      <Container fluid className="auth-section" id="signup">
        <Particle />
        <Container className="auth-content">
          <Row className="align-items-center" style={{ minHeight: "100vh" }}>
            <Col md={6} lg={5} className="mx-auto">
              <div className="auth-card">
                <h1 className="auth-heading">Crear cuenta</h1>
                <p className="auth-subheading">
                  Únete a nuestra comunidad hoy
                </p>

                {error && (
                  <div className="alert alert-danger" role="alert">
                    {error}
                  </div>
                )}

                {success && (
                  <div className="alert alert-success" role="alert">
                    {success}
                  </div>
                )}

                <Form onSubmit={handleSubmit} className="auth-form">
                  {/* FirstName Input */}
                  <Form.Group className="mb-4">
                    <div className="input-wrapper">
                      <FaUser className="input-icon" />
                      <Form.Control
                        type="text"
                        placeholder="Nombre"
                        name="firstname"
                        value={formData.firstname}
                        onChange={handleChange}
                        required
                        className="auth-input"
                      />
                    </div>
                  </Form.Group>

                  {/* LastName Input */}
                  <Form.Group className="mb-4">
                    <div className="input-wrapper">
                      <FaUser className="input-icon" />
                      <Form.Control
                        type="text"
                        placeholder="Apellido"
                        name="lastname"
                        value={formData.lastname}
                        onChange={handleChange}
                        required
                        className="auth-input"
                      />
                    </div>
                  </Form.Group>

                  {/* Email Input */}
                  <Form.Group className="mb-4">
                    <div className="input-wrapper">
                      <FaEnvelope className="input-icon" />
                      <Form.Control
                        type="email"
                        placeholder="Correo electrónico"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
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
                        name="password"
                        value={formData.password}
                        onChange={handleChange}
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

                  {/* Confirm Password Input */}
                  <Form.Group className="mb-4">
                    <div className="input-wrapper">
                      <FaLock className="input-icon" />
                      <Form.Control
                        type={showConfirmPassword ? "text" : "password"}
                        placeholder="Confirmar contraseña"
                        name="confirmPassword"
                        value={formData.confirmPassword}
                        onChange={handleChange}
                        required
                        className="auth-input"
                      />
                      <button
                        type="button"
                        className="show-password-btn"
                        onClick={() =>
                          setShowConfirmPassword(!showConfirmPassword)
                        }
                      >
                        {showConfirmPassword ? "👁️" : "👁️‍🗨️"}
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
                    {loading ? "Registrando..." : "Crear cuenta"}
                  </Button>
                </Form>

                {/* Links */}
                <div className="auth-links">
                  <p>
                    ¿Ya tienes cuenta?{" "}
                    <a href="/login" className="auth-link">
                      Inicia sesión aquí
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

export default Signup;
