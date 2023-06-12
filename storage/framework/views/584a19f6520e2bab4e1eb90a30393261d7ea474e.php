
<?php $__env->startSection('estilos'); ?>
<link href="<?php echo e(asset('assets/datatables/css/dataTables.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo e(asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cabecera'); ?>
Historial de actualizaciones de producto
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Productos</li>
    <li class="active">Historial de act.</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.actualizar-stock-precio', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableHistorial" class="table table-condensed table-striped table-hover" style="font-size: small; width: 100%">
                <thead>
                    <tr>
                        <th class="text-center">Usuario</th>
                        <th class="text-center">Marca</th>
                        <th class="text-center" width="220">Modelo</th>
                        <th class="text-center">Nro. parte</th>
                        <th class="text-center">Detalle</th>
                        <th class="text-center">Comentario</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center" style="width: 10%">Herramientas</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src='<?php echo e(asset("assets/datatables/js/jquery.dataTables.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/js/dataTables.bootstrap.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js")); ?>'></script>
    <script src="<?php echo e(asset('assets/loadingoverlay/loadingoverlay.min.js')); ?>"></script>
    <script src='<?php echo e(asset("mgcp/js/util.js?v=2")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=4")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/producto-view.js?v=4")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/historial-model.js?v=4")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/historial-view.js?v=4")); ?>'></script>

    <script>
        $(document).ready(function () {
            //*****INICIALIZACION*****
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            
            const historialModel = new HistorialProductoModel('<?php echo e(csrf_token()); ?>');
            const historialView = new HistorialProductoView(historialModel);
            historialView.listar();
            const productoModel = new ProductoModel('<?php echo e(csrf_token()); ?>');
            const productoView = new ProductoView(productoModel);
            productoView.obtenerPrecioStockPortalEvent();
        });

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/acuerdo-marco/producto/historial-actualizaciones.blade.php ENDPATH**/ ?>