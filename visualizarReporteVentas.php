<?php require(ROOT_VIEW . '/templates/header.php'); ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Generar Reporte de Ventas</h4>
                <form method="GET" action="<?= HTTP_BASE ?>/reporte/filtrarVentas">
                    <div class="form-group">
                        <label for="fechaInicio">Fecha Inicio:</label>
                        <input type="date" name="fechaInicio" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="fechaFin">Fecha Fin:</label>
                        <input type="date" name="fechaFin" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require(ROOT_VIEW . '/templates/footer.php'); ?>
