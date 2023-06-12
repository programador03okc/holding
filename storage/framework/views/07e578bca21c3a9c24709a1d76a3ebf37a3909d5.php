

<?php $__env->startSection('cabecera'); ?>
Configuración de indicadores
<?php $__env->stopSection(); ?>

<?php $__env->startSection('estilos'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">O/C propias</li>
    <li class="active">Configuración de indicadores</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

 <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="box box-solid">
    <div class="box-body">
        <form id="formActualizar" method="post" action="<?php echo e(route('mgcp.ordenes-compra.propias.indicadores.actualizar-configuracion')); ?>">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <table id="tablePorMonto" class="table table-condensed table-hover" style="font-size: small; width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">Tipo</th>
                                <th class="text-center" style="width: 25%">Rojo</th>
                                <th class="text-center" style="width: 25%">Amarillo</th>
                                <th class="text-center" style="width: 25%">Verde</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $indicadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indicador): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center"><?php echo e($indicador->tipo==1 ? 'Diario' : 'Mensual'); ?></td>
                                <input type="hidden" name="tipo[]" value="<?php echo e($indicador->tipo); ?>">
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">Hasta S/</span>
                                        <input type="text" required placeholder="Monto" class="form-control entero" name="rojo[]" value="<?php echo e(number_format($indicador->rojo)); ?>">
                                    </div>

                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon">Hasta S/</span>
                                        <input type="text" required placeholder="Monto" class="form-control entero" name="amarillo[]" value="<?php echo e(number_format($indicador->amarillo)); ?>">
                                    </div>
                                </td>
                                <td class="text-center">Superior a amarillo</td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <button class="btn btn-primary" type="submit">Actualizar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>


<script src="<?php echo e(asset("mgcp/js/util.js")); ?>"></script>
<script>
    $(document).ready(function() {
        Util.seleccionarMenu(window.location);
        Util.activarSoloEnteros();
        $('#formActualizar').submit(()=>{
            $('button').prop('disabled',true).html(Util.generarPuntosSvg()+'Actualizando');
        });

    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/orden-compra/propia/configuracion-indicadores.blade.php ENDPATH**/ ?>