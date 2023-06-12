

<?php $__env->startSection('cabecera'); ?>
Reporte de cuadros pendientes de cierre
<?php $__env->stopSection(); ?>

<?php $__env->startSection('estilos'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Cuadros de presupuesto</li>
    <li class="active">Reportes</li>
    <li class="active">Pendientes de cierre</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<div class="box box-solid">
    <div class="box-body">
    
        <div class="row">
            <div class="col-sm-12">
            <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <p>Se generará un archivo en Excel con la lista de cuadros pendientes de cierre, con fechas desde el mes anterior hasta fin de este año</p>
                <br>
                <form class="form-horizontal" id="formGenerar" target="_blank" method="post" action="<?php echo e(route('mgcp.cuadro-costos.reportes.pendientes-cierre.generar-archivo')); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-5">
                            <button class="btn btn-primary" type="submit">Generar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>


<script src="<?php echo e(asset('mgcp/js/util.js')); ?>"></script>
<script>
    $(document).ready(function() {
        Util.seleccionarMenu(window.location);
        Util.activarSoloDecimales();
        $('#formActualizar').submit(() => {
            $('button').prop('disabled', true).html(Util.generarPuntosSvg() + 'Actualizando');
        });

    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/cuadro-costo/reporte/pendiente-cierre.blade.php ENDPATH**/ ?>