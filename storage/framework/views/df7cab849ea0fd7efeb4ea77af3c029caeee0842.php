<?php $__env->startSection('cabecera'); ?>
Sin permiso
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<div class="box box-solid">
    <div class="box-body">
        <p>Su usuario no tiene permiso para acceder a este formulario</p>
        <br>
        <div class="text-center">
            <a href="<?php echo e(route('mgcp.home')); ?>" class="btn btn-primary">Salir</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src='<?php echo e(asset("mgcp/js/util.js")); ?>'></script>
<script>
    $(document).ready(function () {
        //*****INICIALIZACION*****
        Util.seleccionarMenu(window.location);
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp\resources\views/mgcp/usuario/sin_permiso.blade.php ENDPATH**/ ?>