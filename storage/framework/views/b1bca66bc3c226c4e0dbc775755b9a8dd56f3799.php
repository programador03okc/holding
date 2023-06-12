<style>
    #modalContacto .form-group {
        margin-bottom: 0px;
    }

</style>

<?php $__env->startSection('cabecera'); ?>
Detalles de oportunidad <?php echo e($oportunidad->codigo_oportunidad); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Oportunidades</li>
    <li><a href="<?php echo e(route('mgcp.oportunidades.lista')); ?>">Lista</a></li>
    <li class="active">Detalles</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Detalles</h3>
        <div class="box-tools pull-right">
            <div class="btn-group" role="group">
                <a href="<?php echo e(route('mgcp.cuadro-costos.detalles',['id'=>$oportunidad->id])); ?>" title="Ver cuadro de costos" class="btn btn-box-tool" id="btn_cuadro_costo"><i class="glyphicon glyphicon-th-large" aria-hidden="true"></i></a>
                <?php if(Auth::user()->tieneRol(5) || $oportunidad->id_responsable == Auth::user()->id): ?>
                <button type="button" data-id="<?php echo e($oportunidad->id); ?>" title="Editar oportunidad" class="btn btn-box-tool editar-oportunidad" data-codigo="<?php echo e($oportunidad->codigo_oportunidad); ?>"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>
                <?php endif; ?>
                <?php if(Auth::user()->tieneRol(20) || $oportunidad->id_responsable == Auth::user()->id): ?>
                <!--<button title="Enviar notificaciones a otras personas" class="btn btn-box-tool" id="btnNotificarUsuarios"><i class="glyphicon glyphicon-bell" aria-hidden="true"></i></button>-->
                <?php endif; ?>
                <button data-toggle="modal" data-target="#modalContacto" data-id="<?php echo e($oportunidad->id); ?>" title="Ver contacto" class="btn btn-box-tool"><i class="glyphicon glyphicon-credit-card" aria-hidden="true"></i></button>
                <!--<button title="Enviar oportunidad por correo" class="btn btn-default" id="btnEnviarCorreo"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></button>-->
                <a target="_blank" href="<?php echo e(route('mgcp.oportunidades.imprimir',$oportunidad->id)); ?>" title="Imprimir" class="btn btn-box-tool"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    <div class="box-body">

        <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('mgcp.partials.errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-md-1 control-label">Cliente:</label>
                <div class="col-md-3">
                    <div class="form-control-static"><?php echo e($oportunidad->entidad->nombre); ?></div>
                </div>
                <label class="col-md-1 control-label">Probab:</label>
                <div class="col-md-2">
                    <div class="form-control-static"><?php echo e(ucwords($oportunidad->probabilidad)); ?></div>
                </div>
                <label class="col-md-2 control-label">Fecha límite:</label>
                <div class="col-md-3">
                    <div class="form-control-static">
                        <?php if($oportunidad->dias_diferencia<4 && ($oportunidad->id_estado==1 || $oportunidad->id_estado==2 || $oportunidad->id_estado==3)): ?>
                            <span class="text-danger"><strong><?php echo e($oportunidad->fecha_limite); ?></strong></span>
                            <?php else: ?>
                            <?php echo e($oportunidad->fecha_limite); ?>

                            <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 control-label">Importe:</label>
                <div class="col-md-3">
                    <div class="form-control-static"><?php echo e($oportunidad->monto); ?></div>
                </div>
                <label class="col-md-1 control-label">Margen:</label>
                <div class="col-md-2">
                    <div class="form-control-static"><?php echo e($oportunidad->margen); ?>%</div>
                </div>
                <label class="col-md-2 control-label">Responsable:</label>
                <div class="col-md-3">
                    <div class="form-control-static"><?php echo e($oportunidad->responsable->name); ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 control-label">Negocio:</label>
                <div class="col-md-3">
                    <div class="form-control-static"><?php echo e($oportunidad->tiponegocio->tipo); ?></div>
                </div>
                <label class="col-md-1 control-label">Grupo:</label>
                <div class="col-md-2">
                    <div class="form-control-static"><?php echo e($oportunidad->grupo->grupo); ?></div>
                </div>
                <label class="col-md-2 control-label">Estado:</label>
                <div class="col-md-3">
                    <div class="form-control-static"><?php echo e($oportunidad->estado->estado); ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 control-label">Oport:</label>
                <div class="col-md-11">
                    <div class="form-control-static text-justify"><?php echo e($oportunidad->oportunidad); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Otros</h3>
    </div>
    <div class="box-body">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab_status">Status (<?php echo e(count($statusOportunidad)); ?>)</a></li>
            <li><a data-toggle="tab" href="#tab_actividades">Actividades (<?php echo e(count($actividades)); ?>)</a></li>
            <li><a data-toggle="tab" href="#tab_comentarios">Comentarios (<?php echo e(count($comentarios)); ?>)</a></li>
        </ul>

        <div class="tab-content">
            <div id="tab_status" class="tab-pane fade in active">
                <br>
                <fieldset>
                    <legend>Status</legend>
                    <div class="row">
                        <div id="div_status" class="col-md-10 col-md-offset-1">
                            <?php if(count($statusOportunidad)>0): ?>
                            <div class="table-responsive">
                                <table style="width: 100%; font-size: 14px;" class="table table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 15%">Fecha</th>
                                            <th class="text-center" style="width: 15%">Usuario</th>
                                            <th class="text-center" style="width: 40%">Detalles</th>
                                            <th class="text-center" style="width: 15%">Estado</th>
                                            <th class="text-center" style="width: 15%">Archivos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $statusOportunidad; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($status->created_at); ?></td>
                                            <td><?php echo e($status->usuario->name); ?></td>
                                            <td class="text-justify"><?php echo e($status->detalle); ?></td>
                                            <td class="text-center"><?php echo e($status->estado->estado); ?></td>
                                            <td class="text-center">
                                                <?php if($status->archivos()->count()>0): ?>
                                                <button data-tipo="status" data-codigo="<?php echo e($status->id); ?>" class="btn btn-default btn-sm ver-archivos-oportunidad"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Ver</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center">Sin status</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </fieldset>
                <?php if(Auth::user()->tieneRol(7) || $oportunidad->id_responsable == Auth::user()->id): ?>

                <br><br>
                <fieldset>
                    <legend>Ingresar status</legend>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <form enctype="multipart/form-data" class="form-horizontal" role="form" method="POST" action="<?php echo e(route('mgcp.oportunidades.ingresar-status')); ?>">
                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                <input type="hidden" name="id" value="<?php echo e($oportunidad->id); ?>">
                                <div class="form-group">
                                    <div class="col-sm-2 control-label">Status:</div>
                                    <div class="col-sm-5">
                                        <textarea placeholder="Ingrese un status" required class="form-control" id="detalle" name="status"></textarea>
                                    </div>
                                    <div class="col-sm-2 control-label">Estado:</div>
                                    <div class="col-sm-3">
                                        <select class="form-control" name="estado" id="selectEstado">
                                            <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($estado->id); ?>" <?php if($estado->id==$oportunidad->id_estado): ?> selected <?php endif; ?>><?php echo e($estado->estado); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 control-label">Archivos:</div>
                                    <div class="col-md-5">
                                        <input type="file" name="archivos[]" multiple="true" class="form-control">
                                        <p class="help-block">Tamaño máximo: <?php echo e(ini_get("upload_max_filesize")); ?>B</p>
                                    </div>
                                </div>
                                <br>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </fieldset>
                <?php endif; ?>
            </div>

            <div id="tab_actividades" class="tab-pane fade">
                <br>
                <fieldset class="fieldset">
                    <legend>Actividades</legend>
                    <div class="row">
                        <div id="div_actividades" class="col-md-10 col-md-offset-1">
                            <?php if(count($actividades)>0): ?>
                            <div class="table-responsive">
                                <table style="width: 100%; font-size: 14px" class="table table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 15%">Inicio</th>
                                            <th class="text-center" style="width: 15%">Fin</th>
                                            <th class="text-center" style="width: 15%">Usuario</th>
                                            <th class="text-center" style="width: 40%">Detalles</th>
                                            <th class="text-center" style="width: 15%">Archivos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $actividades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actividad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($actividad->fecha_inicio); ?></td>
                                            <td class="text-center"><?php echo e($actividad->fecha_fin); ?></td>
                                            <td><?php echo e($actividad->usuario->name); ?></td>
                                            <td><?php echo e($actividad->detalle); ?></td>
                                            <td class="text-center">
                                                <?php if($actividad->archivos()->count()>0): ?>
                                                <button data-tipo="actividades" data-codigo="<?php echo e($actividad->id); ?>" class="btn btn-default btn-sm ver-archivos-oportunidad"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Ver</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php else: ?>
                            <div class="text-center">Sin actividades</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </fieldset>
                <?php if(Auth::user()->tieneRol(7) || $oportunidad->id_responsable == Auth::user()->id): ?>
                <br>
                <fieldset>
                    <legend>Ingresar actividad</legend>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <form enctype="multipart/form-data" class="bloquear form-horizontal" role="form" method="POST" action="<?php echo e(route('mgcp.oportunidades.ingresar-actividad')); ?>">
                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                <input type="hidden" name="id" value="<?php echo e($oportunidad->id); ?>">
                                <div class="form-group">
                                    <div class="col-sm-2 control-label">Fecha inicio:</div>
                                    <div class="col-sm-4">
                                        <input type="text" placeholder="dd-mm-aaaa" required class="form-control date-picker" name="fecha_inicio" value="<?php echo e(date('d-m-Y')); ?>">
                                    </div>
                                    <div class="col-sm-2 control-label">Fecha fin:</div>
                                    <div class="col-sm-4">
                                        <input autocomplete="off" type="text" placeholder="dd-mm-aaaa" required class="form-control date-picker" name="fecha_fin">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 control-label">Detalle de actividad:</div>
                                    <div class="col-md-4">
                                        <textarea placeholder="Ingrese detalle de actividad" required class="form-control" name="detalle_actividad"></textarea>
                                    </div>
                                    <div class="col-md-2 control-label">Archivos:</div>
                                    <div class="col-md-4">
                                        <input type="file" name="archivos[]" multiple="true" class="form-control">
                                        <p class="help-block">Tamaño máximo: <?php echo e(ini_get("upload_max_filesize")); ?>B</p>
                                    </div>
                                </div>
                                <br>
                                <div class="text-center">
                                    <button type="submit" class="bloquear btn btn-primary">Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </fieldset>
                <?php endif; ?>
            </div>
            <div id="tab_comentarios" class="tab-pane fade in">
                <br>
                <fieldset class="fieldset">
                    <legend>Comentarios</legend>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <?php if(count($comentarios)>0): ?>
                            <div class="table-responsive">
                                <table style="width: 100%; font-size: 14px;" class="table table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 25%">Fecha</th>
                                            <th class="text-center" style="width: 25%">Usuario</th>
                                            <th class="text-center" style="width: 50%">Comentario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $comentarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comentario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($comentario->created_at); ?></td>
                                            <td><?php echo e($comentario->publicado_por); ?></td>
                                            <td><?php echo e($comentario->comentario); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center">Sin comentarios</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </fieldset>
                <?php if(Auth::user()->tieneRol(7) || $oportunidad->id_responsable == Auth::user()->id): ?>
                <br>
                <fieldset>
                    <legend>Ingresar comentario</legend>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <form enctype="multipart/form-data" class="bloquear form-horizontal" role="form" method="POST" action="<?php echo e(route('mgcp.oportunidades.ingresar-comentario')); ?>">
                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                <input type="hidden" name="id" value="<?php echo e($oportunidad->id); ?>">
                                <div class="form-group">
                                    <div class="col-sm-2 col-sm-offset-1 control-label">Comentario:</div>
                                    <div class="col-md-8">
                                        <textarea placeholder="Ingrese un comentario" required class="form-control" name="comentario"></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="text-center">
                                    <button type="submit" class="bloquear btn btn-primary">Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </fieldset>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div id="modalContacto" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Ver contacto</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Nombre:</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?php echo e($oportunidad->nombre_contacto); ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Teléfono:</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?php echo e($oportunidad->telefono_contacto); ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Correo:</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?php echo e($oportunidad->correo_contacto); ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Cargo:</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?php echo e($oportunidad->cargo_contacto); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalEnviarCorreo" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar oportunidad por correo</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="codigo" value="<?php echo e($oportunidad->id); ?>">
                    <div class="form-group">
                        <label class="col-md-2 control-label">A:</label>
                        <div class="col-sm-5">
                            <input type="text" id="txtCorreoDestinatario" class="form-control validar" name="correo" placeholder="Correo">
                        </div>
                        <div class="col-sm-5">
                            <select class="form-control" name="dominio">
                                <option value="okcomputer">@okcomputer.com.pe</option>
                                <option value="proyectec">@proyectec.com.pe</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Mensaje:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control validar" name="mensaje" placeholder="Mensaje para destinatario"></textarea>
                        </div>
                    </div>
                    <div class="form-group mensaje">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnCorreoAceptar" class="btn btn-success">Enviar</button>
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
                <div class="text-center mensaje-inicial"></div>
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


<div id="modalNotificacionesOtros" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar notificaciones a otras personas</h4>
            </div>
            <div class="modal-body">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">Lista de notificados</h4>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed" id="tableNotificarCorreos" style="width: 100%; font-size: small">

                            <tbody>
                                <?php $__currentLoopData = $notificaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notificacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="width: 90%"><?php echo e($notificacion->correo); ?></td>
                                    <td style="width: 10%" class="text-center"><button data-codigo="<?php echo e($notificacion->id); ?>" type="button" class="btn btn-sm btn-default retirar" title="Retirar"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        <small class="help-block">Nota: Esta lista no incluye al creador de la oportunidad ni a los usuarios que reciben notificaciones de todas las oportunidades</small>
                    </div>
                </div>
                <div class="box box-solid last">
                    <div class="box-header with-border">
                        <h4 class="box-title">Agregar correo</h4>
                    </div>
                    <div class="box-body">

                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-sm-5">
                                    <input type="text" class="form-control validar" id="txtCorreoNotificar" name="correo" placeholder="Correo">
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control" id="cmb_dominio" name="dominio">
                                        <option value="okcomputer">@okcomputer.com.pe</option>
                                        <option value="proyectec">@proyectec.com.pe</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <button data-codigo="<?php echo e($oportunidad->id); ?>" type="button" class="btn btn-default" id="btnAgregarCorreo" title="Agregar">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mensaje">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div id="modalArchivosOportunidad" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Archivos adjuntos</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje">
                </div>
                <div class="contenido">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<link href="<?php echo e(asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")); ?>" rel="stylesheet" type="text/css" />
<script src="<?php echo e(asset("assets/jquery-number/jquery.number.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js")); ?>"></script>
<script src="<?php echo e(asset("mgcp/js/util.js")); ?>"></script>

<script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-view.js?v=10")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-model.js?v=10")); ?>'></script>

<script>
    $(document).ready(function() {
        const url = "<?php echo e(url('/')); ?>";
        $('input.number').number(true, 2);
        $('input.entero').number(true, 0);
        Util.seleccionarMenu("<?php echo e(route('mgcp.oportunidades.lista')); ?>");
        Util.activarDatePicker();
        Util.activarLimiteSubidaArchivos('<?php echo e(ini_get("upload_max_filesize")); ?>');
        const token = '<?php echo e(csrf_token()); ?>';
        const oportunidadView = new OportunidadView(new OportunidadModel(token));
        oportunidadView.editarEvent(true);
        oportunidadView.registrarOtrosEvent();
        oportunidadView.verArchivosEvent();


        //*****NOTIFICACIONES A OTRAS PERSONAS 
        /*$('#modalNotificacionesOtros').on('click', 'button.retirar', function() {
            var $boton = $(this);
            $boton.attr('disabled', true);
            $.ajax({
                url: '<?php echo e(route("mgcp.oportunidades.retirar-notificacion")); ?>'
                , data: {
                    codigo: $boton.data('codigo')
                    , _token: '<?php echo e(csrf_token()); ?>'
                }
                , type: 'POST'
                , dataType: 'json'
                , success: function(json) {
                    if (json.tipo == 'success') {
                        $boton.closest('tr').fadeOut(500, function() {
                            $(this).remove();
                        });
                    }
                }
                , error: function(xhr, status) {
                    alert('Hubo un problema al retirar el correo. Por favor actualice la página e intente de nuevo.');
                    $boton.attr('disabled', false);
                }
            });
        });*/

        /*$('#txtCorreoNotificar, #txt_correo_msj').keypress(function(e) {
            if (e.which === 32 || e.which === 64) {
                return false;
            }
        });

        $('#btnAgregarCorreo').click(function() {
            var $boton = $(this);
            $boton.attr('disabled', true);
            $.ajax({
                url: '<?php echo e(route("mgcp.oportunidades.agregar-notificacion")); ?>'
                , data: {
                    codigo: $boton.data('codigo')
                    , correo: $('#txtCorreoNotificar').val()
                    , dominio: $('#cmb_dominio').val()
                    , _token: '<?php echo e(csrf_token()); ?>'
                }
                , type: 'POST'
                , dataType: 'json'
                , success: function(data) {
                    if (data.tipo == 'success') {
                        var $tbody = $('#tableNotificarCorreos').find('tbody');
                        $tbody.append('<tr></tr>');
                        $tbody.find('tr').last().append('<td style="width: 90%">' + $('#txtCorreoNotificar').val() + $('#cmb_dominio').find('option:selected').text() + '</td>' +
                            '<td style="width: 10%" class="text-center">' +
                            '<button data-codigo="' + data.id + '" type="button" class="btn btn-sm btn-default retirar" title="Retirar">' +
                            '<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>' +
                            '</button>' +
                            '</td>').fadeIn(1000);
                        $('#txtCorreoNotificar').val('');
                    } else {
                        alert(data.mensaje);
                    }

                }
                , error: function(xhr, status) {
                    alert('Hubo un problema al registrar el correo. Por favor actualice la página e inténtelo de nuevo.');
                }
                , complete: function(xhr, status) {
                    $boton.attr('disabled', false);
                }
            });
        });
        $('#btn_fecha_factura').click(function() {
            var $modal = $('#modal_facturacion');
            $('#txtCorreoNotificar').val('');
            $("#span_resultado").html("");
            $modal.modal('show');
        });
        $('#btnNotificarUsuarios').click(function() {
            $('#modalNotificacionesOtros').modal('show');
            $('#txtCorreoNotificar').val('');
        });

        $('#btnEnviarCorreo').click(function() {
            var $modal = $('#modalEnviarCorreo');
            $modal.find('input[type=text]').val('');
            $modal.find('textarea').val('');
            $modal.modal('show');
        });*/

        /*$('#btnCorreoAceptar').click(function() {
            var $modal = $('#modalEnviarCorreo');
           
            $.ajax({
                url: '<?php echo e(route("mgcp.oportunidades.enviar-correo")); ?>'
                , data: $modal.find('form').serialize()
                , type: 'POST'
                , dataType: 'json'
                , beforeSend: function() {
                    $modal.find('div.mensaje').html('<div class="text-center">Enviando...</div>');
                    $modal.find('input[type=text]').attr('disabled', true);
                    $modal.find('select').attr('disabled', true);
                    $modal.find('button').attr('disabled', true);
                    $modal.find('textarea').attr('disabled', true);
                }
                , error: function(xhr, status) {
                    alert('Hubo un problema al enviar el correo. Por favor actualice la página e inténte de nuevo.');
                }
                , success: function(data) {
                    if (data.mensaje == 'enviado') {
                        alert("El correo ha sido enviado a " + data.correo + ".")
                        $modal.modal('hide');
                    } else {
                        Util.mensaje($modal.find('div.mensaje'), data.tipo, data.mensaje);
                    }
                }
                , complete: function(xhr, status) {
                    $modal.find('input[type=text]').attr('disabled', false);
                    $modal.find('select').attr('disabled', false);
                    $modal.find('button').attr('disabled', false);
                    $modal.find('textarea').attr('disabled', false);
                    $modal.find('div.mensaje').html('');
                }
            });
        });*/

        /*$('#select_grupo').change(function () {
         obtenerTiposNegocio('');
         });*/

        /*function obtenerTiposNegocio(cod_tipo_negocio)
         {
         $.ajax({
         url: "",
         type: 'post',
         dataType: 'json',
         data: {id: $('#select_grupo').val(), _token: '<?php echo e(csrf_token()); ?>'},
         beforeSend: function () {
         $('#div_tipo_negocio').html('<p class="form-control-static">Cargando...</p>');
         },
         success: function (datos) {
         var contenido = '<select class="form-control" id="select_tipo_negocio" name="tipo_negocio">';
         for (var indice in datos) {
         contenido += '<option value="' + datos[indice].id_tipo + '">' + datos[indice].tipo + '</option>';
         }
         contenido += '</select>';
         $('#div_tipo_negocio').html(contenido);
         if (cod_tipo_negocio !== '')
         {
         $('#select_tipo_negocio').val(cod_tipo_negocio);
         }
         }
         });
         }*/
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp\resources\views/mgcp/oportunidad/detalles.blade.php ENDPATH**/ ?>