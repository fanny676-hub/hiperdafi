<?php require(ROOT_VIEW . '/templates/header.php') ?>
<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0 font-weight-bold">Bienvenido!!</h3>
                <p></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 d-flex grid-margin stretch-card">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="card">
                <a href="<?php echo HTTP_BASE; ?>/ventas/ventas">
                    <button type="button" class="btn btn-primary btn-lg btn-block">
                        <i class="typcn typcn-user"></i>
                        Ventas
                    </button>
                </a>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Reporte</h4>
                        <p class="card-description">
                            Reporte Ventas por fecha
                        </p>
                        <form class="forms-sample" method="POST" action="<?php echo HTTP_BASE;?>/reporte/filtrarVentas">
                            <div class="form-group">
                                <label for="exampleInputUsername1">Fecha Inicio</label>
                                <input type="date" class="form-control" id="exampleInputUsername1" placeholder="Username" name="fecha_inicio">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Fecha Fin</label>
                                <input type="date" class="form-control" id="exampleInputEmail1" placeholder="Email" name="fecha_fin">
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Generar Reporte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
    <!-- partial -->
</div>
<!-- main-panel ends -->
<?php require(ROOT_VIEW . '/templates/footer.php') ?>