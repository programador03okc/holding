

<?php $__env->startSection('cabecera'); ?>
    Exportar proformas
<?php $__env->stopSection(); ?>

<?php $__env->startSection('estilos'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <ol class="breadcrumb">
        <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
        <li class="active">Acuerdo marco</li>
        <li class="active">Proformas</li>
        <li class="active">Exportar</li>
    </ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

    <div class="box box-solid">
        <div class="box-body">
            <p>El sistema generará un archivo Excel con las proformas de acuerdo a los filtros seleccionados</p>
            <br>
            <form class="form-horizontal" method="POST" target="_blank" action="<?php echo e(route('mgcp.acuerdo-marco.proformas.exportar.generar-archivo')); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Empresa</label>
                    <div class="col-sm-2">
                        <select class="form-control" name="empresa">
                            <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option selected value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->empresa); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">F. emisión desde</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control date-picker" name="fechaEmisionDesde" value="<?php echo e(date('d-m-Y')); ?>">
                    </div>
                    <label class="col-sm-2 control-label">F. emisión hasta</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control date-picker" name="fechaEmisionHasta" value="<?php echo e(date('d-m-Y')); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Catálogo</label>
                    <div class="col-sm-2">
                        <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10" name="catalogo[]">
                            <?php $__currentLoopData = $catalogos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catalogo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option selected value="<?php echo e($catalogo->id); ?>"><?php echo e($catalogo->descripcion); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">Estado</label>
                    <div class="col-sm-2">
                        <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10" name="estado[]">
                            <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option selected value="<?php echo e($estado->estado); ?>"><?php echo e($estado->estado); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">Tiene comentario</label>
                    <div class="col-sm-2">
                        <select class="selectpicker" data-width="100%" data-actions-box="true" multiple data-size="2" name="comentario[]">
                            <option selected value="1">Sí</option>
                            <option selected value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Tipo</label>
                    <div class="col-sm-2">
                        <select class="selectpicker" data-width="100%" data-actions-box="true" multiple data-size="2" name="tipo[]">
                            <option selected value="co">Compra ordinaria</option>
                            <option selected value="gc">Gran compra</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 col-sm-offset-6">
                        <button class="btn btn-primary">Exportar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href='<?php echo e(asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")); ?>' rel="stylesheet" type="text/css" />
<script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")); ?>'></script>
<script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js")); ?>'></script>

<link href='<?php echo e(asset("assets/bootstrap-select/css/bootstrap-select.min.css")); ?>' rel="stylesheet" type="text/css" />
<script src='<?php echo e(asset("assets/bootstrap-select/js/bootstrap-select.min.js")); ?>'></script>
<script src='<?php echo e(asset("assets/bootstrap-select/js/i18n/defaults-es_ES.js")); ?>'></script>

    <script src="<?php echo e(asset('mgcp/js/util.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            Util.activarDatePicker();
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/acuerdo-marco/proforma/exportar.blade.php ENDPATH**/ ?>