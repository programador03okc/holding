<?php $__env->startSection('contenido'); ?>

<?php $__env->startSection('cabecera'); ?> Cambiar claves de Peru Compras <?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Empresas</li>
    <li class="active">Cambiar claves</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>
<div class="box box-solid">
    <div class="box-body">
        <form class="form-horizontal" method="POST" action="<?php echo e(route('mgcp.acuerdo-marco.empresas.actualizar-claves')); ?>" style="padding: 20px;">
            <input type="hidden" name="empresa" value="<?php echo e($empresa->id); ?>">
            <div class="form-group">
                <label class="col-sm-4 control-label">Nueva clave U1</label>
                <div class="col-sm-4">
                    <input type="password" name="clave_uno" class="form-control" placeholder="Ingrese nueva clave del usuario 1">
                    <div class="help-block text-justify">Dejar en blanco si no desea cambiar la clave del usuario 1</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Nueva clave U2</label>
                <div class="col-sm-4">
                    <input type="password" name="clave_dos" class="form-control" placeholder="Ingrese nueva clave del usuario 2">
                    <div class="help-block text-justify">Dejar en blanco si no desea cambiar la clave del usuario 2</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Nueva clave U3</label>
                <div class="col-sm-4">
                    <input type="password" name="clave_tres" class="form-control" placeholder="Ingrese nueva clave del usuario 3">
                    <div class="help-block text-justify">Dejar en blanco si no desea cambiar la clave del usuario 3</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4 text-center">
                    <button type="submit" class="btn btn-primary">Actualizar claves</button>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4">
                    <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo $__env->make('mgcp.partials.errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src='<?php echo e(asset("mgcp/js/util.js")); ?>'></script>
    <script>
        $(document).ready(function () {
            Util.seleccionarMenu(window.location);
            $('form').submit(function () {
                $('button[type=submit]').prop('disabled', true);
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/acuerdo-marco/empresa/cambiar-claves.blade.php ENDPATH**/ ?>