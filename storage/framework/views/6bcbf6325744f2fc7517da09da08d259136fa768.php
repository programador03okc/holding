

<?php $__env->startSection('cabecera'); ?> Lista de oportunidades <?php $__env->stopSection(); ?>

<?php $__env->startSection('estilos'); ?>
    <style>
        small.help-block {
            margin-bottom: 0px;
        }

        div.modal li {
            margin-bottom: 5px;
        }

        .group-table {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Oportunidades</li>
    <li class="active">Lista</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>


<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableOportunidades" class="table table-condensed table-hover table-striped" style="font-size: small; width: 100%">
                <thead>
                    <tr>
                        <th style="width: 11%" class="text-center">Cliente</th>
                        <th style="width: 13%" class="text-center">Oportunidad</th>
                        <th style="width: 6%" class="text-center">Prob.</th>
                        <th style="width: 13%" class="text-center">Status</th>
                        <th style="width: 6%" class="text-center">Importe</th>
                        <th style="width: 6%" class="text-center">Fecha<br>creación</th>
                        <th style="width: 6%" class="text-center">Fecha<br>límite</th>
                        <th style="width: 7%" class="text-center">Margen</th>
                        <th style="width: 8%" class="text-center">Responsable</th>
                        <th style="width: 6%" class="text-center">Estado</th>
                        <th style="width: 6%" class="text-center">Grupo</th>
                        <th style="width: 6%" class="text-center">Tipo</th>
                        <th style="width: 6%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEliminarOportunidad" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Eliminar oportunidad</h4>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de eliminar la oportunidad?</p>
                <div class="detalles">
                </div>
                <div class="mensaje-final">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEliminarOportunidadAceptar" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarOportunidad" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar Oportunidad <span class="codigo-oportunidad"></span></h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <form class="form-horizontal" id="formEditarOportunidad">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="id" id="txtIdOportunidad">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <!--<input type="text" disabled class="form-control" name="cliente" placeholder="Cliente">-->
                            <select name="cliente" class="form-control">
                                <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cliente->id); ?>"><?php echo e($cliente->nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php if(Auth::user()->tieneRol(4)): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Responsable</label>
                        <div class="col-sm-10">
                            <select name="responsable" class="form-control">
                                <?php $__currentLoopData = $responsables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $responsable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($responsable->id); ?>"><?php echo e($responsable->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Oportunidad</label>
                        <div class="col-sm-10">
                            <textarea name="oportunidad" class="form-control" required placeholder="Oportunidad"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Probabilidad</label>
                        <div class="col-sm-4">
                            <select name="probabilidad" class="form-control">
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Límite</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="Fecha límite" required name="fecha_limite" class="form-control validar date-picker">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Moneda</label>
                        <div class="col-sm-4">
                            <select name="tipo_moneda" class="form-control">
                                <option value="d">$</option>
                                <option value="s">S/</option>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Importe</label>
                        <div class="col-sm-4">
                            <input type="text" name="importe" required class="form-control validar number" placeholder="Importe">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Margen %</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control entero" required name="margen" placeholder="Margen">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Grupo</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="grupo" id="select_grupo">
                                <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($grupo->id); ?>"><?php echo e($grupo->grupo); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label">Tipo</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="tipo_negocio">
                                <?php $__currentLoopData = $tiposNegocio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tipo->id); ?>"><?php echo e($tipo->tipo); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Contacto</label>
                        <div class="col-sm-4">
                            <input type="text" name="nombre_contacto" class="form-control" placeholder="Nombre">
                        </div>
                        <label class="col-sm-2 control-label">Cargo</label>
                        <div class="col-sm-4">
                            <input type="text" name="cargo_contacto" class="form-control" placeholder="Cargo">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Teléfono</label>
                        <div class="col-sm-4">
                            <input type="text" name="telefono_contacto" class="form-control" placeholder="Teléfono">
                        </div>
                        <label class="col-sm-2 control-label">Correo</label>
                        <div class="col-sm-4">
                            <input type="text" name="correo_contacto" class="form-control" placeholder="Correo">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Reportado por</label>
                        <div class="col-sm-10">
                            <input maxlength="100" list="personas" type="text" class="form-control" placeholder="Nombre" name="reportado_por">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 mensaje-final">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" id="btnEditarOportunidadAceptar" class="btn btn-primary">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFiltros" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Filtros</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal" id="formFiltros">
                    <p><small>Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</small></p>
                    <fieldset class="group-table">
                        <?php echo csrf_field(); ?>

                        <?php if(Auth::user()->tieneRol(45)): ?>
                        <div class="form-group">
                            <div class="col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" checked> Sólo veo oportunidades donde soy responsable
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkFechaLimite" id="chkFechaLimite" <?php if(session('oport_fecha_limite_desde')!==null): ?> checked <?php endif; ?>> Fecha límite
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="<?php if(session('oport_fecha_limite_desde')!==null): ?><?php echo e(session('oport_fecha_limite_desde')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>" class="form-control date-picker" name="fechaLimiteDesde" placeholder="dd-mm-aaaa">
                                <small class="help-block">Desde</small>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="<?php if(session('oport_fecha_limite_hasta')!==null): ?><?php echo e(session('oport_fecha_limite_hasta')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>" class="form-control date-picker" name="fechaLimiteHasta" placeholder="dd-mm-aaaa">
                                <small class="help-block">Hasta</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkFechaCreacion" id="chkFechaCreacion" <?php if(session('oport_fecha_creacion_desde')!==null): ?> checked <?php endif; ?>> Fecha creación
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="<?php if(session('oport_fecha_creacion_desde')!==null): ?><?php echo e(session('oport_fecha_creacion_desde')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>" class="form-control date-picker" name="fechaCreacionDesde" placeholder="dd-mm-aaaa">
                                <small class="help-block">Desde</small>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" value="<?php if(session('oport_fecha_creacion_hasta')!==null): ?><?php echo e(session('oport_fecha_creacion_hasta')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>" class="form-control date-picker" name="fechaCreacionHasta" placeholder="dd-mm-aaaa">
                                <small class="help-block">Hasta</small>
                            </div>
                        </div>
                        <?php if(!Auth::user()->tieneRol(45)): ?>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkResponsable" id="chkResponsable" <?php if(session('oport_responsable')!==null): ?> checked <?php endif; ?>> Responsable
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" name="selectResponsable">
                                    <?php $__currentLoopData = $responsables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $responsable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($responsable->id); ?>" <?php if(session('oport_responsable')==$responsable->id): ?> selected <?php endif; ?>><?php echo e($responsable->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkTipoNegocio" id="chkTipoNegocio" <?php if(session('oport_tipo_negocio')!==null): ?> checked <?php endif; ?>> Tipo negocio
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" name="selectTipoNegocio">
                                    <?php $__currentLoopData = $tiposNegocio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tipo->id); ?>" <?php if(session('oport_tipo_negocio')==$tipo->id): ?> selected <?php endif; ?>><?php echo e($tipo->tipo); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkProbabilidad" id="chkProbabilidad" <?php if(session('oport_probabilidad')!==null): ?> checked <?php endif; ?>> Probabilidad
                                    </label>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <select class="form-control" name="selectProbabilidad">
                                    <option value="alta" <?php if(session('oport_probabilidad')=='alta' ): ?> selected <?php endif; ?>>Alta</option>
                                    <option value="media" <?php if(session('oport_probabilidad')=='media' ): ?> selected <?php endif; ?>>Media</option>
                                    <option value="baja" <?php if(session('oport_probabilidad')=='baja' ): ?> selected <?php endif; ?>>Baja</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="chkEstado" id="chkEstado" <?php if(session('oport_estado')!==null): ?> checked <?php endif; ?>> Estado
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" name="selectEstado">
                                    <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($estado->id); ?>" <?php if(session('oport_estado')==$estado->id): ?> selected <?php endif; ?>><?php echo e($estado->estado); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalStatus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Status</h4>
            </div>
            <div class="modal-body text-justify" id="divStatusOportunidad">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<form id="frm_imprimir" action="" method="POST" target="_blank">
    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
    <div class="codigos">

    </div>
</form>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link href='<?php echo e(asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")); ?>' rel="stylesheet" type="text/css" />
    <script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js")); ?>'></script>

    <link href='<?php echo e(asset("assets/datatables/css/dataTables.bootstrap.min.css")); ?>' rel="stylesheet" type="text/css" />
    <link href='<?php echo e(asset("assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css")); ?>' rel="stylesheet" type="text/css" />

    <script src='<?php echo e(asset("assets/datatables/js/jquery.dataTables.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/js/dataTables.bootstrap.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js")); ?>'></script>

    <script src='<?php echo e(asset("assets/jquery-number/jquery.number.min.js")); ?>'></script>
    <link href='<?php echo e(asset("assets/lobibox/dist/css/lobibox.min.css")); ?>' rel="stylesheet" type="text/css" />
    <script src='<?php echo e(asset("assets/lobibox/dist/js/lobibox.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/loadingoverlay/loadingoverlay.min.js")); ?>'></script>

    <script src='<?php echo e(asset("mgcp/js/util.js?v=11")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-view.js?v=11")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-model.js?v=10")); ?>'></script>
    <script>
        /*$(document).ajaxStart(function() {
            Pace.restart()
        });*/
        $(document).ready(function() {

            //*****INICIALIZACION*****
            Util.seleccionarMenu(window.location);
            Util.activarSoloEnteros();
            Util.activarDatePicker();
            $('input.number').number(true, 2);

            const token = '<?php echo e(csrf_token()); ?>';
            const idUsuario = "<?php echo e(Auth::user()->id); ?>";
            const permisos = {
                puedeEditar: "<?php echo e(Auth::user()->tieneRol(5)); ?>",
                puedeEliminar: "<?php echo e(Auth::user()->tieneRol(6)); ?>"
            };
            const oportunidadView = new OportunidadView(new OportunidadModel(token));
            oportunidadView.listarTodas(idUsuario, permisos);
            oportunidadView.editarEvent();
            oportunidadView.eliminarEvent();
            oportunidadView.verStatusEvent();
            Util.activarFiltros('#tableOportunidades', oportunidadView.model);
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/oportunidad/lista.blade.php ENDPATH**/ ?>