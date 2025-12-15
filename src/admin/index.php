<?php

// admin/index.php

$page_title = "Dashboard Principal";

include 'header.php';

include 'navbar.php';

include 'sidebar.php';

// Conexión y conteos dinámicos
require_once __DIR__ . '/../conexionpdo.php';
require_once __DIR__ . '/../includes/models/Vendedor.php';
require_once __DIR__ . '/../includes/models/Venta.php';

$vendedorCount = 0;
$ventaCount = 0;
$calendarEvents = [];
try {
    $vModel = new Vendedor($pdo);
    $vendedorCount = (int)$vModel->contar();

    $veModel = new Venta($pdo);
    $ventaCount = (int)$veModel->contar();

    // Preparar eventos para el calendario: ventas por fecha
    $stmt = $pdo->prepare("SELECT DATE(fecha) AS d, COUNT(*) AS cnt FROM ventas GROUP BY DATE(fecha) ORDER BY DATE(fecha) DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        if (!empty($r['d'])) {
            $calendarEvents[] = [
                'title' => (int)$r['cnt'] . ' ventas',
                'start' => $r['d']
            ];
        }
    }

    // Obtener top 10 vendedores por número de ventas
    $stmtChart = $pdo->prepare("
        SELECT ven.vendedor, COUNT(v.id_venta) as total_ventas
        FROM vendedor ven
        LEFT JOIN ventas v ON v.id_vendedor = ven.id
        GROUP BY ven.id, ven.vendedor
        ORDER BY total_ventas DESC
        LIMIT 10
    ");
    $stmtChart->execute();
    $vendedoresChart = $stmtChart->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // En caso de error con la BD, dejamos los valores por defecto (0)
    $vendedorCount = $vendedorCount ?: 0;
    $ventaCount = $ventaCount ?: 0;
}

?>


<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>Dashboard</h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>

                        <li class="breadcrumb-item active">Dashboard</li>

                    </ol>

                </div>

            </div>

        </div><!-- /.container-fluid -->

    </section>


    <!-- Main content -->

    <section class="content">

        <div class="container-fluid">

            <!-- Small boxes (Stat box) -->

            <div class="row">

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-info">

                        <div class="inner">

                            <h3><?php echo (int)$vendedorCount; ?></h3>

                            <p>Total Vendedores</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-users"></i>

                        </div>

                        <a href="../modules/vendedores/index.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-success">

                        <div class="inner">

                            <h3><?php echo (int)$ventaCount; ?></h3>

                            <p>Total Ventas</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-chart-line"></i>

                        </div>

                        <a href="../modules/ventas/historial.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-warning">

                        <div class="inner">

                            <h3><?php echo (int)$vendedorCount; ?></h3>

                            <p>Vendedores Activos</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-user-check"></i>

                        </div>

                        <a href="../modules/vendedores/index.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

                <div class="col-lg-3 col-6">

                    <!-- small box -->

                    <div class="small-box bg-danger">

                        <div class="inner">

                            <h3>65</h3>

                            <p>Ventas Pendientes</p>

                        </div>

                        <div class="icon">

                            <i class="fas fa-clock"></i>

                        </div>

                        <a href="../modules/ventas/historial.php" class="small-box-footer">

                            Más info <i class="fas fa-arrow-circle-right"></i>

                        </a>

                    </div>

                </div>

                <!-- ./col -->

            </div>

            <!-- /.row -->


            <!-- Main row -->

            <div class="row">

                <!-- Left col -->

                <section class="col-lg-7 connectedSortable">

                    <!-- Custom tabs (Charts with tabs)-->

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-chart-pie mr-1"></i>

                                Ventas por Vendedor

                            </h3>

                        </div><!-- /.card-header -->

                        <div class="card-body">

                            <div class="tab-content p-0">

                                <!-- Morris chart - Sales -->

                                <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">

                                    <canvas id="ventasChart" height="300" style="height: 300px;"></canvas>

                                </div>

                            </div>

                        </div><!-- /.card-body -->

                    </div>

                    <!-- /.card -->

                </section>

                <!-- /.Left col -->


                <!-- right col (We are only adding the ID to make the widgets sortable)-->

                <section class="col-lg-5 connectedSortable">

                    <!-- Calendar -->

                    <div class="card card-primary card-outline">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="far fa-calendar-alt mr-1"></i>

                                Calendario de Ventas

                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>

                        </div>

                        <!-- /.card-header -->

                        <div class="card-body p-3">

                            <div id="calendar"></div>

                        </div>

                        <!-- /.card-body -->

                    </div>

                    <!-- /.card -->

                </section>

                <!-- right col -->

            </div>

            <!-- /.row (main row) -->

        </div><!-- /.container-fluid -->

    </section>

    <!-- /.content -->

</div>

<!-- /.content-wrapper -->


<?php include 'footer.php'; ?>


<script>

// Gráfico de ventas por vendedor (dinámico desde BD)
var ctx = document.getElementById('ventasChart').getContext('2d');
var ventasChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php 
            echo implode(',', array_map(function($v) { 
                return '"' . htmlspecialchars($v['vendedor'], ENT_QUOTES) . '"'; 
            }, $vendedoresChart ?? [])); 
        ?>],
        datasets: [{
            label: 'Número de Ventas',
            data: [<?php 
                echo implode(',', array_map(function($v) { 
                    return (int)$v['total_ventas']; 
                }, $vendedoresChart ?? [])); 
            ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(199, 199, 199, 0.2)',
                'rgba(83, 102, 255, 0.2)',
                'rgba(255, 99, 255, 0.2)',
                'rgba(99, 255, 132, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(199, 199, 199, 1)',
                'rgba(83, 102, 255, 1)',
                'rgba(255, 99, 255, 1)',
                'rgba(99, 255, 132, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + ' ventas';
                    }
                }
            }
        }
    }
});

</script>

<!-- FullCalendar CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            list: 'Lista'
        },
        eventClassNames: 'bg-info',
        eventDisplay: 'block',
        displayEventTime: false,
        eventColor: '#17a2b8',
        eventTextColor: '#ffffff',
        dayCellClassNames: 'calendar-day',
        events: <?php echo json_encode($calendarEvents, JSON_UNESCAPED_UNICODE); ?>,
        eventClick: function(info) {
            alert('Fecha: ' + info.event.start.toLocaleDateString('es-ES') + '\n' + info.event.title);
        },
        eventMouseEnter: function(info) {
            info.el.style.cursor = 'pointer';
        }
    });

    calendar.render();
});
</script>

<style>
/* Custom calendar styles */
#calendar {
    font-size: 0.9rem;
}
#calendar .fc-daygrid-day-number {
    padding: 5px;
    font-size: 0.85rem;
}
#calendar .fc-event {
    border-radius: 3px;
    padding: 2px 5px;
    margin-bottom: 2px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
}
#calendar .fc-toolbar-title {
    font-size: 1.2rem;
    font-weight: 600;
}
#calendar .fc-button {
    padding: 0.25rem 0.5rem;
    font-size: 0.85rem;
}
#calendar .fc-day-today {
    background-color: rgba(23, 162, 184, 0.1) !important;
}
</style>