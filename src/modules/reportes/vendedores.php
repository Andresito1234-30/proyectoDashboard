<?php
// modules/reportes/vendedores.php
declare(strict_types=1);
session_start();

$page_title = "Reporte de Top Vendedores";

require_once __DIR__ . '/../../admin/header.php';
require_once __DIR__ . '/../../admin/navbar.php';
require_once __DIR__ . '/../../admin/sidebar.php';
require_once __DIR__ . '/../../conexionpdo.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-trophy mr-2"></i>Top Vendedores</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/admin/index.php">Inicio</a></li>
            <li class="breadcrumb-item">Reportes</li>
            <li class="breadcrumb-item active">Top Vendedores</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <!-- Filtros -->
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtrar por Período</h3>
        </div>
        <div class="card-body">
          <form method="GET" action="">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Fecha Inicio</label>
                  <input type="date" name="fecha_inicio" class="form-control" 
                         value="<?php echo $_GET['fecha_inicio'] ?? date('Y-m-01'); ?>">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Fecha Fin</label>
                  <input type="date" name="fecha_fin" class="form-control" 
                         value="<?php echo $_GET['fecha_fin'] ?? date('Y-m-d'); ?>">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search mr-1"></i> Buscar
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <?php
      // Obtener fechas del filtro
      $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
      $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

      // Consulta de top vendedores
      $sql = "SELECT ven.id, ven.vendedor, ven.direccion,
                     COUNT(v.id_venta) as total_ventas,
                     CAST(SUM(v.monto) AS DECIMAL(10,2)) as total_monto
              FROM vendedor ven
              LEFT JOIN ventas v ON v.id_vendedor = ven.id 
                  AND DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin
              GROUP BY ven.id, ven.vendedor, ven.direccion
              ORDER BY total_monto DESC, total_ventas DESC";
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
          'fecha_inicio' => $fechaInicio,
          'fecha_fin' => $fechaFin
      ]);
      $vendedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <!-- Podio Top 3 -->
      <?php if (count($vendedores) >= 3): ?>
      <div class="row">
        <!-- Segundo Lugar -->
        <div class="col-md-4">
          <div class="card card-widget widget-user-2">
            <div class="widget-user-header bg-secondary">
              <div class="widget-user-image">
                <span class="badge badge-pill badge-light" style="font-size: 2rem; padding: 20px;">2</span>
              </div>
              <h3 class="widget-user-username"><?php echo htmlspecialchars($vendedores[1]['vendedor']); ?></h3>
              <h5 class="widget-user-desc">Segundo Lugar</h5>
            </div>
            <div class="card-footer p-0">
              <ul class="nav flex-column">
                <li class="nav-item">
                  <span class="nav-link">
                    Ventas <span class="float-right badge bg-info"><?php echo $vendedores[1]['total_ventas']; ?></span>
                  </span>
                </li>
                <li class="nav-item">
                  <span class="nav-link">
                    Monto <span class="float-right badge bg-success">S/ <?php echo number_format((float)$vendedores[1]['total_monto'], 2); ?></span>
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Primer Lugar -->
        <div class="col-md-4">
          <div class="card card-widget widget-user-2">
            <div class="widget-user-header bg-warning">
              <div class="widget-user-image">
                <span class="badge badge-pill badge-light" style="font-size: 2rem; padding: 20px;">🏆</span>
              </div>
              <h3 class="widget-user-username"><?php echo htmlspecialchars($vendedores[0]['vendedor']); ?></h3>
              <h5 class="widget-user-desc">Primer Lugar</h5>
            </div>
            <div class="card-footer p-0">
              <ul class="nav flex-column">
                <li class="nav-item">
                  <span class="nav-link">
                    Ventas <span class="float-right badge bg-info"><?php echo $vendedores[0]['total_ventas']; ?></span>
                  </span>
                </li>
                <li class="nav-item">
                  <span class="nav-link">
                    Monto <span class="float-right badge bg-success">S/ <?php echo number_format((float)$vendedores[0]['total_monto'], 2); ?></span>
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Tercer Lugar -->
        <div class="col-md-4">
          <div class="card card-widget widget-user-2">
            <div class="widget-user-header bg-danger">
              <div class="widget-user-image">
                <span class="badge badge-pill badge-light" style="font-size: 2rem; padding: 20px;">3</span>
              </div>
              <h3 class="widget-user-username"><?php echo htmlspecialchars($vendedores[2]['vendedor']); ?></h3>
              <h5 class="widget-user-desc">Tercer Lugar</h5>
            </div>
            <div class="card-footer p-0">
              <ul class="nav flex-column">
                <li class="nav-item">
                  <span class="nav-link">
                    Ventas <span class="float-right badge bg-info"><?php echo $vendedores[2]['total_ventas']; ?></span>
                  </span>
                </li>
                <li class="nav-item">
                  <span class="nav-link">
                    Monto <span class="float-right badge bg-success">S/ <?php echo number_format((float)$vendedores[2]['total_monto'], 2); ?></span>
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Tabla completa -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-list mr-1"></i> Todos los Vendedores</h3>
        </div>
        <div class="card-body">
          <table id="tablaVendedores" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Vendedor</th>
                <th>Dirección</th>
                <th>Total Ventas</th>
                <th>Monto Total</th>
                <th>Promedio</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $posicion = 1;
              foreach ($vendedores as $v): 
              ?>
                <tr>
                  <td>
                    <?php 
                    if ($posicion === 1) echo '<i class="fas fa-trophy text-warning"></i>';
                    elseif ($posicion === 2) echo '<i class="fas fa-medal text-secondary"></i>';
                    elseif ($posicion === 3) echo '<i class="fas fa-medal text-danger"></i>';
                    else echo $posicion;
                    ?>
                  </td>
                  <td><?php echo htmlspecialchars($v['vendedor']); ?></td>
                  <td><?php echo htmlspecialchars($v['direccion']); ?></td>
                  <td><?php echo number_format($v['total_ventas']); ?></td>
                  <td>S/ <?php echo number_format((float)$v['total_monto'], 2); ?></td>
                  <td>S/ <?php echo $v['total_ventas'] > 0 ? number_format((float)$v['total_monto'] / $v['total_ventas'], 2) : '0.00'; ?></td>
                </tr>
              <?php 
              $posicion++;
              endforeach; 
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../../admin/footer.php'; ?>

<script>
$(document).ready(function() {
  $('#tablaVendedores').DataTable({
    responsive: true,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    },
    order: [[5, 'desc']]
  });
});
</script>
