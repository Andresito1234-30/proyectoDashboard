# Proyecto PHP con Autenticación MongoDB
# Link del proyecto en la web: https://huarotodashboard.infinityfreeapp.com 
## 📁 Estructura del Proyecto

```
src/
├── services/          # Servicios de API (comunicación con backend)
│   └── api.js         # Cliente API con fetch, manejo de tokens
├── hooks/             # Hooks de autenticación
│   └── useAuth.js     # Manejo de sesión y autenticación
├── assets/
│   ├── css/           # Estilos
│   └── js/
│       ├── auth_handler.js      # Manejador de login/registro
│       └── login_registrarse.js # Toggle entre login/signup
├── admin/             # Dashboard administrativo
│   ├── index.php      # Panel principal
│   ├── header.php     # Header común
│   ├── sidebar.php    # Menú lateral
│   └── navbar.php     # Barra navegación
├── includes/          # Archivos PHP compartidos
│   ├── auth.php
│   ├── config.php
│   └── functions.php
├── modules/           # Módulos de vendedores y ventas
│   ├── vendedores/
│   └── ventas/
├── index.php          # Página principal login/registro
├── google_login.php   # Login con Google OAuth
└── logout.php         # Cerrar sesión
```

## 🔧 Tecnologías

- **Frontend:** HTML5, CSS3, JavaScript ES6+ (Modules)
- **Backend API:** Node.js + Express + MongoDB (YARNDBBackend/)
- **Backend PHP:** AdminLTE, Bootstrap 4, MySQL (ventas/vendedores)
- **Auth:** JWT tokens + localStorage

## 🚀 Configuración

### 1. Backend Node.js

```bash
cd YARNDBBackend
npm install
npm run dev
```

El servidor correrá en `http://localhost:3977/api/v1`

### 2. Backend PHP (XAMPP)

- Asegúrate de tener Apache y MySQL corriendo
- Base de datos para ventas/vendedores en MySQL
- Usuarios nuevos se guardan en MongoDB

### 3. Variables de entorno

En `src/services/api.js`:
```javascript
const API_URL = 'http://localhost:3977/api/v1';
```

## 📝 Flujo de Autenticación

### Registro
1. Usuario completa formulario en `index.php`
2. `auth_handler.js` → `AuthManager.signup()` → `authAPI.register()`
3. Backend Node crea usuario en MongoDB
4. Auto-login y redirección a `/admin/index.php`

### Login
1. Usuario ingresa email/password en `index.php`
2. `auth_handler.js` → `AuthManager.login()` → `authAPI.login()`
3. Backend retorna `{ access, user }`
4. Se guarda en `localStorage` y redirige a dashboard

### Login con Google
1. Usuario hace clic en botón Google
2. `handleCredentialResponse()` envía `id_token` a `google_login.php`
3. PHP valida token y crea sesión
4. Redirección a dashboard

## 🔐 Gestión de Tokens

- **Access Token:** Almacenado en `localStorage.accessToken`
- **Usuario:** Almacenado en `localStorage.user` (JSON)
- **Expiración:** Si API retorna 401, se limpia storage y redirige a login

## 📦 Archivos Clave

### `src/services/api.js`
Cliente API con interceptores automáticos para:
- Añadir Authorization header
- Manejar errores 401 (sesión expirada)
- Métodos: `get()`, `post()`, `put()`, `delete()`

### `src/hooks/useAuth.js`
Gestor de autenticación:
- `login(email, password)`
- `signup(userData)`
- `logout()`
- `isAuthenticated()`
- `getCurrentUser()`

### `src/assets/js/auth_handler.js`
Manejador de formularios:
- Validación client-side
- Integración con AuthManager
- Mensajes de error/éxito
- Auto-login post-registro

## 🎨 UI/UX

- **Diseño:** Formulario deslizante (login ↔ signup)
- **Iconos:** Font Awesome 6.5.1
- **Google Sign-In:** Google Identity Services (GIS)
- **Responsive:** Compatible con móviles

## 🐛 Debug

Abre la consola del navegador (F12) para ver:
- Peticiones a la API
- Errores de validación
- Estado de autenticación

## 📄 Endpoints API

### Auth
- `POST /api/v1/auth/register` - Registrar usuario
- `POST /api/v1/auth/login` - Iniciar sesión
- `POST /api/v1/auth/refresh-access-token` - Refrescar token

### Response Format
```json
{
  "ok": true,
  "msg": "Inicio de sesión exitoso",
  "access": "eyJhbGc...",
  "user": {
    "_id": "...",
    "firstname": "Juan",
    "lastname": "Pérez",
    "email": "juan@example.com"
  }
}
```

## ✅ Cambios Realizados

- ✅ Creada estructura modular `services/` y `hooks/`
- ✅ Eliminados archivos duplicados (login.php, signup.php, auth_proxy.php, etc.)
- ✅ Implementado cliente API con fetch
- ✅ Gestión de sesión con localStorage
- ✅ Formularios con validación client-side
- ✅ Auto-login después de registro
- ✅ Manejo de sesión expirada
- ✅ Integración con backend MongoDB

## 🔄 Próximos Pasos

1. Probar registro completo
2. Verificar login y redirección
3. Confirmar que el dashboard carga correctamente
4. Validar que avatares y datos de usuario se muestran
