<?php
// admin/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
// No session_start ni funciones aquí. Usamos las del header.
$avatar = normalize_avatar_url($_SESSION['ruta_foto'] ?? null, $_SESSION['google_picture'] ?? null);
$nombre = current_username();

// Contar ventas para el badge
require_once __DIR__ . '/../conexionpdo.php';
require_once __DIR__ . '/../includes/models/Venta.php';
$totalVentas = 0;
try {
    $veModel = new Venta($pdo);
    $totalVentas = (int)$veModel->contar();
} catch (Exception $e) {
    $totalVentas = 0;
}
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="/admin/index.php" class="brand-link text-center">
    <img src="/assets/img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity:.8">
    <span class="brand-text font-weight-light"><b>Admin</b>Panel</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?php echo htmlspecialchars($avatar); ?>" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="/admin/profile.php" class="d-block"><?php echo htmlspecialchars($nombre); ?></a>
        <small class="text-success"><i class="fas fa-circle text-success"></i> En línea</small>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline mt-2">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Buscar..." aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="/admin/index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Vendedores -->
        <li class="nav-item <?php echo strpos($_SERVER['REQUEST_URI'], '/modules/vendedores/') !== false ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/modules/vendedores/') !== false ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Vendedores<i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="/modules/vendedores/index.php" class="nav-link <?php echo ($current_page == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/modules/vendedores/') !== false) ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Lista de Vendedores</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/modules/vendedores/crear.php" class="nav-link <?php echo $current_page == 'crear.php' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Agregar Vendedor</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Ventas -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>Ventas<i class="right fas fa-angle-left"></i><span class="badge badge-info right"><?php echo $totalVentas; ?></span></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="/modules/ventas/crear.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Nueva Venta</p></a></li>
            <li class="nav-item"><a href="/modules/ventas/index.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Historial</p></a></li>
          </ul>
        </li>

        <!-- Reportes -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Reportes<i class="right fas fa-angle-left"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="/modules/reportes/ventas.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Ventas por Período</p></a></li>
            <li class="nav-item"><a href="/modules/reportes/vendedores.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Top Vendedores</p></a></li> 
          </ul>
        </li>

        <!-- Configuración -->
        <li class="nav-item">
          <a href="/modules/configuracion/general.php" class="nav-link">
            <i class="nav-icon fas fa-cogs"></i>
            <p>Configuración</p>
          </a>
        </li>

        <!-- Portafolio -->
        <li class="nav-item">
          <a href="https://andresito1234-30.github.io/portafolioHuarotoMongoDB/" target="_blank" class="nav-link">
            <i class="nav-icon fas fa-briefcase"></i>
            <p>
              Portafolio
              <i class="fas fa-external-link-alt ml-1" style="font-size: 0.7rem;"></i>
            </p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
