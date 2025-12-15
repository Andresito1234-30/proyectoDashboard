<?php

$page_title = "Detalle del Vendedor";

include '../../admin/header.php';

include '../../admin/navbar.php';

include '../../admin/sidebar.php';


include '../../includes/config.php';

include '../../includes/models/Vendedor.php';


$database = new Database();

$db = $database->getConnection();

$vendedor = new Vendedor($db);


// Obtener datos del vendedor

if(isset($_GET['id'])) {

    $vendedor->id = $_GET['id'];

    

    if(!$vendedor->leerUno()) {

        $_SESSION['error'] = "Vendedor no encontrado.";

        header("Location: index.php");

        exit();

    }

} else {

    $_SESSION['error'] = "ID de vendedor no especificado.";

    header("Location: index.php");

    exit();

}

?>


<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>Detalle del Vendedor</h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item"><a href="../../admin/index.php">Inicio</a></li>

                        <li class="breadcrumb-item"><a href="index.php">Vendedores</a></li>

                        <li class="breadcrumb-item active">Detalle</li>

                    </ol>

                </div>

            </div>

        </div><!-- /.container-fluid -->

    </section>


    <!-- Main content -->

    <section class="content">

        <div class="container-fluid">

            <div class="row">

                <div class="col-md-8 mx-auto">

                    <div class="card card-info">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="fas fa-user"></i> 

                                Información del Vendedor

                            </h3>

                            <div class="card-tools">

                                <a href="editar.php?id=<?php echo $vendedor->id; ?>" class="btn btn-warning btn-sm">

                                    <i class="fas fa-edit"></i> Editar

                                </a>

                            </div>

                        </div>

                        

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4 text-center">

                                    <div class="user-profile mb-4">

                                        <img src="/assets/img/user-avatar.jpg" 

                                             class="img-circle elevation-2" 

                                             alt="User Image" 

                                             width="120">

                                        <h4 class="mt-3"><?php echo htmlspecialchars($vendedor->vendedor); ?></h4>

                                        <span class="badge badge-primary">ID: <?php echo $vendedor->id; ?></span>

                                    </div>

                                </div>

                                

                                <div class="col-md-8">

                                    <table class="table table-bordered">

                                        <tr>

                                            <th width="30%">ID:</th>

                                            <td>

                                                <span class="badge badge-primary"><?php echo $vendedor->id; ?></span>

                                            </td>

                                        </tr>

                                        <tr>

                                            <th>Vendedor:</th>

                                            <td>

                                                <strong><?php echo htmlspecialchars($vendedor->vendedor); ?></strong>

                                            </td>

                                        </tr>

                                        <tr>

                                            <th>Dirección:</th>

                                            <td><?php echo htmlspecialchars($vendedor->direccion); ?></td>

                                        </tr>

                                        <tr>

                                            <th>Fecha de Venta:</th>

                                            <td>

                                                <span class="badge badge-info">

                                                    <?php echo date('d/m/Y', strtotime($vendedor->fechaventa)); ?>

                                                </span>

                                            </td>

                                        </tr>

                                    </table>

                                </div>

                            </div>

                        </div>

                        

                        <div class="card-footer">

                            <a href="index.php" class="btn btn-default">

                                <i class="fas fa-arrow-left"></i> Volver a la lista

                            </a>

                            <div class="float-right">

                                <small class="text-muted">

                                    Última actualización: <?php echo date('d/m/Y H:i:s'); ?>

                                </small>

                            </div>

                        </div>

                    </div>

                    <!-- /.card -->

                </div>

            </div>

        </div>

    </section>

    <!-- /.content -->

</div>

<!-- /.content-wrapper -->


<?php include '../../admin/footer.php'; ?>