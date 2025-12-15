<?php
// modules/reportes/ventas.php
declare(strict_types=1);
session_start();

$page_title = "Reporte de Ventas por Período";

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
          <h1><i class="fas fa-chart-line mr-2"></i>Ventas por Período</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/admin/index.php">Inicio</a></li>
            <li class="breadcrumb-item">Reportes</li>
            <li class="breadcrumb-item active">Ventas por Período</li>
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
          <form method="GET" action="" id="filterForm">
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

      // Consulta de ventas por período
      $sql = "SELECT DATE(v.fecha) as fecha, COUNT(*) as total_ventas, 
                     CAST(SUM(v.monto) AS DECIMAL(10,2)) as total_monto
              FROM ventas v
              WHERE DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin
              GROUP BY DATE(v.fecha)
              ORDER BY DATE(v.fecha) DESC";
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
          'fecha_inicio' => $fechaInicio,
          'fecha_fin' => $fechaFin
      ]);
      $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Calcular totales
      $totalVentas = 0;
      $totalMonto = 0;
      foreach ($ventas as $v) {
          $totalVentas += (int)$v['total_ventas'];
          $totalMonto += (float)$v['total_monto'];
      }
      ?>

      <!-- Resumen -->
      <div class="row">
        <div class="col-md-6">
          <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total de Ventas</span>
              <span class="info-box-number"><?php echo number_format($totalVentas); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Monto Total</span>
              <span class="info-box-number">S/ <?php echo number_format($totalMonto, 2); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de resultados -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-table mr-1"></i> Detalle por Fecha</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-sm btn-success" onclick="exportarExcel()">
              <i class="fas fa-file-excel mr-1"></i> Exportar
            </button>
          </div>
        </div>
        <div class="card-body">
          <table id="tablaReporte" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Total Ventas</th>
                <th>Monto Total</th>
                <th>Promedio por Venta</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ventas as $v): ?>
                <tr>
                  <td><?php echo date('d/m/Y', strtotime($v['fecha'])); ?></td>
                  <td><?php echo number_format($v['total_ventas']); ?></td>
                  <td>S/ <?php echo number_format((float)$v['total_monto'], 2); ?></td>
                  <td>S/ <?php echo number_format((float)$v['total_monto'] / $v['total_ventas'], 2); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr class="bg-light font-weight-bold">
                <td>TOTAL</td>
                <td><?php echo number_format($totalVentas); ?></td>
                <td>S/ <?php echo number_format($totalMonto, 2); ?></td>
                <td>S/ <?php echo $totalVentas > 0 ? number_format($totalMonto / $totalVentas, 2) : '0.00'; ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once __DIR__ . '/../../admin/footer.php'; ?>

<script>
$(document).ready(function() {
  $('#tablaReporte').DataTable({
    responsive: true,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    },
    order: [[0, 'desc']]
  });
});

function exportarExcel() {
  window.location.href = 'ventas_export.php?fecha_inicio=<?php echo $fechaInicio; ?>&fecha_fin=<?php echo $fechaFin; ?>';
}
</script>
