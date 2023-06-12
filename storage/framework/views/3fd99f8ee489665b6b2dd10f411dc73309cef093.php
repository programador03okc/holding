

<?php $__env->startSection('cabecera'); ?>
Nueva oportunidad
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Oportunidades</li>
    <li class="active">Nueva</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>


<div class="box box-solid">
    <div class="box-body">
        <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('mgcp.partials.errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form class="form-horizontal bloquear-boton" id="formCrearOportunidad" role="form" method="POST" action="<?php echo e(route('mgcp.oportunidades.registrar')); ?>">
            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
            <input type="hidden" name="id_empresa" value="<?php echo e(Auth::user()->id_empresa); ?>">

            <?php if(Auth::user()->tieneRol(4)): ?>
            <div class="form-group">
                <label class="col-sm-2 control-label">Código</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control disabled" id="codigo" name="codigo" value="<?php echo e($codigo); ?>" disabled>
                </div>
                <label class="col-sm-2 control-label">Responsable *</label>
                <div class="col-sm-4">
                    <select name="responsable" class="form-control">
                        <?php $__currentLoopData = $responsables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $responsable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($responsable->id); ?>" <?php if($responsable->id == Auth::user()->id): ?> selected <?php endif; ?>><?php echo e($responsable->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label for="codigo" class="col-sm-2 control-label">Código</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control disabled" id="codigo" name="codigo" value="<?php echo e($codigo); ?>" disabled>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label class="col-sm-2 control-label">Cliente *</label>
                <div class="col-sm-4">
                    <select class="form-control select2" name="cliente" id="selectEntidad" style="width: 100%;">
                    </select>
                    <a href="#" id="aNuevaEntidad">Nuevo cliente</a>
                </div>
                <label class="col-sm-2 control-label">Reportado por</label>
                <div class="col-sm-4">
                    <input maxlength="100" list="personas" type="text" class="form-control" placeholder="Persona que reportó la oportunidad" name="reportado_por" value="<?php echo e(old('reportado_por')); ?>">
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label">Oportunidad *</label>
                <div class="col-sm-10">
                    <textarea class="form-control" placeholder="Descripción de la oportunidad de negocio" name="oportunidad" required=""><?php echo e(old('oportunidad')); ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Probabilidad *</label>
                <div class="col-sm-4">
                    <select name="probabilidad" class="form-control">
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                <label class="col-sm-2 control-label">Fecha límite *</label>
                <div class="col-sm-4">
                    <input type="text" autocomplete="off" placeholder="dd-mm-aaaa" required name="fecha_limite" value="<?php echo e(old('fecha_limite')); ?>" class="form-control date-picker">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Status *</label>
                <div class="col-sm-10">
                    <textarea class="form-control" placeholder="Status o avance de la oportunidad" name="status"><?php echo e(old('fecha_limite')); ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Moneda *</label>
                <div class="col-sm-1">
                    <select name="tipo_moneda" class="form-control">
                        <option value="s">S/</option>
                        <option value="d">$</option>
                    </select>
                </div>
                <label class="col-sm-1 control-label">Importe *</label>
                <div class="col-sm-2">
                    <input type="text" placeholder="Importe" required="" name="importe" value="<?php echo e(old('importe')); ?>" class="form-control number">
                </div>
                <label class="col-sm-2 control-label">Margen (%) *</label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input required="" maxlength="3" placeholder="Porcentaje de margen" type="text" name="margen" value="<?php echo e(old('margen')); ?>" class="form-control entero">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Grupo *</label>
                <div class="col-sm-4">
                    <select name="grupo" id="select_grupo" class="form-control">
                        <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($grupo->id); ?>"><?php echo e($grupo->grupo); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <label class="col-sm-2 control-label">Tipo de negocio *</label>
                <div class="col-sm-4">
                    <select class="form-control" name="tipo_negocio">
                        <?php $__currentLoopData = $tiposNegocio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tipo->id); ?>"><?php echo e($tipo->tipo); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Nombre del contacto</label>
                <div class="col-sm-4">
                    <input type="text" maxlength="100" name="nombre_contacto" value="<?php echo e(old('nombre_contacto')); ?>" placeholder="Nombre" class="form-control">
                </div>
                <label class="col-sm-2 control-label">Cargo del contacto</label>
                <div class="col-sm-4">
                    <input type="text" maxlength="100" name="cargo_contacto" value="<?php echo e(old('cargo_contacto')); ?>" placeholder="Cargo" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Teléfono del contacto</label>
                <div class="col-sm-4">
                    <input type="tel" maxlength="45" name="telefono_contacto" value="<?php echo e(old('telefono_contacto')); ?>" placeholder="Teléfono" class="form-control">
                </div>
                <label class="col-sm-2 control-label">Correo del contacto</label>
                <div class="col-sm-4">
                    <input type="email" maxlength="100" name="correo_contacto" value="<?php echo e(old('correo_contacto')); ?>" placeholder="Correo electrónico" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <div class="form-control-static">Los campos con asterisco (*) son obligatorios</div>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" id="btnRegistrarOportunidad">Registrar</button>
            </div>
        </form>

    </div>
</div>

<div class="modal fade" id="modalNuevaEntidad" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo Cliente</h4>
            </div>
            <div class="modal-body">
                <form id="formNuevaEntidad" class="form-horizontal">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">DNI /RUC *</label>
                        <div class="col-sm-9">
                            <input type="text" maxlength="11" min="8" class="form-control" required placeholder="DNI / RUC" name="ruc">
                            <small class="help-block">Debe tener 8 dígitos para DNI u 11 para RUC</small>
                            <div class="mensaje"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nombre *</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control validar" required placeholder="Nombre / Razón social" name="nombre">
                            <div class="mensaje"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion" class="col-sm-3 control-label">Dirección</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Dirección" name="direccion" />
                            <small class="help-block">Ejemplo: AV. SIMON BOLIVAR NRO 344</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion" class="col-sm-3 control-label">Ubigeo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Ubigeo" name="ubigeo" />
                            <small class="help-block">Ejemplo: LIMA / LIMA / PUEBLO LIBRE</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telefono" class="col-sm-3 control-label">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" placeholder="Teléfono" name="telefono" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3">
                            <div class="form-control-static">Los campos con asteriscos (*) son obligatorios</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12" id="divNuevaEntidadMensaje">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnNuevaEntidadRegistrar" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href='<?php echo e(asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")); ?>' rel="stylesheet" type="text/css" />
<script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")); ?>'></script>
<script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js")); ?>'></script>
<link href='<?php echo e(asset("assets/select2/css/select2.css")); ?>' rel="stylesheet" type="text/css" />
<script src='<?php echo e(asset("assets/select2/js/select2.min.js")); ?>'></script>
<script src='<?php echo e(asset("assets/select2/js/i18n/es.js")); ?>'></script>
<script src='<?php echo e(asset("assets/jquery-number/jquery.number.min.js")); ?>'></script>
<link href='<?php echo e(asset("assets/lobibox/dist/css/lobibox.min.css")); ?>' rel="stylesheet" type="text/css" />
<script src='<?php echo e(asset("assets/lobibox/dist/js/lobibox.min.js")); ?>'></script>

<script src='<?php echo e(asset("mgcp/js/util.js?v=20")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=20")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=20")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-view.js?v=20")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-model.js?v=20")); ?>'></script>


<script>
    $(document).ready(function() {

        Util.seleccionarMenu(window.location);
        Util.activarDatePicker();
        Util.activarSoloEnteros();
        $('input.number').number(true, 2);

        const token = '<?php echo e(csrf_token()); ?>';
        const oportunidadView = new OportunidadView(new OportunidadModel(token));
        const entidadView = new EntidadView(new EntidadModel(token));
        entidadView.nuevaEvent();
        entidadView.buscarEvent();
        oportunidadView.nuevaEvent();
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/oportunidad/nueva.blade.php ENDPATH**/ ?>