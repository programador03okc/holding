

<?php $__env->startSection('estilos'); ?>
    <style>
        .dataTables_wrapper .dataTables_filter input[type="search"] {
            width: 450px;
        }
        #tableProductos {
            color: #000;
        }
        @media (max-width: 968px) {
            .dataTables_wrapper .dataTables_filter input[type="search"] {
                width: auto;
            }
        }
        .help-block {
            margin-bottom: 0px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cabecera'); ?>
Proformas de <?php echo e($tipoProforma==1 ? 'compra ordinaria' : 'gran compra'); ?> individual
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Proformas</li>
    <li class="active">Individual</li>
    <li class="active"><?php echo e($tipoProforma==1 ? 'Compra ordinaria' : 'Gran compra'); ?></li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.actualizar-stock-precio', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.detalles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.historial-actualizaciones', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.orden-compra.publica.ofertas-por-producto', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.entidad.detalles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableProformas" class="table table-striped table-condensed table-hover" style="width: 100%; font-size: x-small;">
                <thead>
                    <tr>
                        <th style="width: 8%" class="text-center">Requerimiento</th>
                        <th style="width: 8%" class="text-center">Proforma</th>
                        <th class="text-center">Entidad</th>
                        <th style="width: 6%" class="text-center">F.emisión<br>/ F.límite</th>
                        <th style="width: 8%" class="text-center">Categoría</th>
                        <th style="width: 9%" class="text-center">Producto</th>
                        <th style="width: 9%" class="text-center">Nro. parte</th>
                        <th style="width: 8%" class="text-center" title="Inicio de entrega / Fin de entrega">InicioEnt.<br>/ FinEnt.</th>
                        <th style="width: 6%" title="Herramientas" class="text-center">Herram.</th>
                        <th style="width: 5%" title="Empresa" class="text-center">Emp.</th>
                        <th style="width: 10%" class="text-center">Lugar de<br>entrega</th>
                        <th style="width: 5%" title="Precio unitario base / Software educativo" class="text-center">Prec.Un.B./<br>Soft.Educ.</th>
                        <th class="text-center">Cant.</th>
                        <th style="width: 7%" class="text-center">Estado</th>
                        <?php if($tipoProforma=='2'): ?>
                        <th style="width: 5%" class="text-center">Plazo<br>entrega</th>
                        <?php endif; ?>
                        <th style="width: 5%" class="text-center">Precio<br>publicar</th>
                        <th style="width: 5%" class="text-center">Flete<br>publicar</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
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
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="fom-control-static mensaje-filtros">Seleccione los filtros que desee aplicar y cierre este cuadro para continuar</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFechaEmision" <?php if(session('proformaFechaEmisionDesde')!==null): ?> checked <?php endif; ?>> Fecha de emisión
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEmisionDesde" class="form-control date-picker" value="<?php if(session('proformaFechaEmisionDesde')!==null): ?><?php echo e(session('proformaFechaEmisionDesde')); ?><?php else: ?><?php echo e($fechaActual->addMonths(-1)->format('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEmisionHasta" class="form-control date-picker" value="<?php if(session('proformaFechaEmisionHasta')!==null): ?><?php echo e(session('proformaFechaEmisionHasta')); ?><?php else: ?><?php echo e($fechaActual->addMonths(1)->format('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFechaLimite" <?php if(session('proformaFechaLimiteDesde')!==null): ?> checked <?php endif; ?>> Fecha límite
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaLimiteDesde" class="form-control date-picker" value="<?php if(session('proformaFechaLimiteDesde')!==null): ?><?php echo e(session('proformaFechaLimiteDesde')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaLimiteHasta" class="form-control date-picker" value="<?php if(session('proformaFechaLimiteHasta')!==null): ?><?php echo e(session('proformaFechaLimiteHasta')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEmpresa" <?php if(session('proformaEmpresas')!==null): ?> checked <?php endif; ?>> Empresa
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEmpresa" class="form-control">
                                <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('proformaEmpresas') && in_array($empresa->id,session('proformaEmpresas'))): ?>
                                    selected
                                    <?php endif; ?>
                                    value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->empresa); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCatalogo" <?php if(session('proformaCatalogos')!==null): ?> checked <?php endif; ?>> Catálogos
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectCatalogo[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                <?php $__currentLoopData = $catalogos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catalogo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('proformaCatalogos') && in_array($catalogo->id,session('proformaCatalogos'))): ?>
                                    selected
                                    <?php endif; ?>
                                    value="<?php echo e($catalogo->id); ?>"><?php echo e($catalogo->catalogo); ?> (<?php echo e($catalogo->acuerdo_marco); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkDepartamento" <?php if(session('proformaDepartamentos')!==null): ?> checked <?php endif; ?>> Departamentos
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectDepartamento[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('proformaDepartamentos') && in_array($departamento->id,session('proformaDepartamentos'))): ?>
                                    selected
                                    <?php endif; ?>
                                    value="<?php echo e($departamento->id); ?>"><?php echo e($departamento->nombre); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstado" <?php if(session('proformaEstado')!==null): ?> checked <?php endif; ?>> Estado
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEstado" class="form-control">
                                <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($estado->estado); ?>" <?php echo e(session('proformaEstado')==$estado->estado ? 'selected' : ''); ?>><?php echo e($estado->estado); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkMarca" <?php if(session('proformaMarcas')!==null): ?> checked <?php endif; ?>> Marca
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectMarca[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                <?php $__currentLoopData = $marcas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('proformaMarcas') && in_array($marca->marca,session('proformaMarcas'))): ?>
                                    selected
                                    <?php endif; ?>
                                    value="<?php echo e($marca->marca); ?>"><?php echo e($marca->marca); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProformasEnviar" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Enviar cotizaciones a Perú Compras</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial">
                </div>
                <p>Seleccione las proformas cotizadas que desea enviar al portal y haga clic en el botón Enviar para continuar. Puede filtrar la lista por empresa o usuario que realizó la cotización. Al filtrar la lista, las proformas ocultas permanecen seleccionadas</p>
                <div class="table-responsive">
                    <table class="table table-condensed table-hover table-striped" style="font-size: x-small; width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 3%" class="text-center">N°</th>
                                <th style="width: 7%" class="text-center">Proforma</th>
                                <th style="width: 7%" class="text-center">Producto</th>
                                <th style="width: 7%" class="text-center">Part N°</th>
                                <th style="width: 9%" class="text-center">Empresa<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarEmpresa">
                                </th>
                                <th style="width: 15%" class="text-center">Lugar entrega</th>
                                <th class="text-center">Fecha límite<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarFechaLimite">
                                </th>
                                <th style="width: 9%" class="text-center">Última edición<br>
                                    <input autocomplete="off" type="text" size="4" id="txtBuscarUsuario">
                                </th>
                                <th class="text-center">Precio publicar</th>
                                <th class="text-center">Flete publicar</th>
                                <th class="text-center">Selec.<br>
                                    <input type="checkbox" id="chkSeleccionarTodo">
                                </th>
                                <th width="12%" class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProformasEnviar">
                        </tbody>
                    </table>
                </div>
                <div id="divProformasEnviarMensaje">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="text" class="btn btn-primary" id="btnEnviarProformas">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUltimaActualizacionLista" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Última actualización de lista</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <div class="table-responsive">
                    <table class="table table-condensed table-hover table-striped" style="width: 100%;font-size: small;">
                        <thead>
                            <tr>
                                <th style="width: 25%" class="text-center">Empresa</th>
                                <th style="width: 35%" class="text-center">Realizada por</th>
                                <th class="text-center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUltimaActualizacionLista">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a target="_blank" href="<?php echo e(route('mgcp.acuerdo-marco.descargar.proformas.index')); ?>" class="btn btn-default">Ir a descarga de proformas</a>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalIngresarFletePorLote" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ingresar flete por lote</h4>
            </div>
            <div class="modal-body">
                <p>Sólo se ingresará el flete a las siguientes proformas:</p>
                <ul>
                    <li>Con estado PENDIENTE</li>
                    <li>Que no tengan flete ingresado</li>
                    <li>Que sean parte del resultado de los filtros aplicados (no se toma en cuentra el criterio de búsqueda ingresado)</li>
                </ul>
                <p>El monto del flete se ingresará de forma automática dependiendo del precio del producto</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnIngresarFletePorLote">Ingresar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeshacerCotizacion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Deshacer cotización</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <p>Información de la proforma:</p>
                <ul>
                    <li>Número: <span class="proforma"></span></li>
                    <li>Requer.: <span class="requerimiento"></span></li>
                    <li>Producto: <span class="producto"></span></li>
                    <li>Entidad: <span class="entidad"></span></li>
                    <li>Empresa: <span class="empresa"></span></li>
                    <li>Precio publicar: <span class="precio-publicar"></span></li>
                    <li>Flete publicar: <span class="flete-publicar"></span></li>
                </ul>
                <p>Al deshacer la cotización, esta proforma podrá volverse a cotizar. Los precios ingresados no se eliminarán. ¿Desea continuar?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnDeshacerCotizacion">Deshacer cotización</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFondosDisponibles" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Fondos disponibles de proveedores</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableFondosProforma" style="width: 100%" class="table table-condensed table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60%">Descripción</th>
                                <th class="text-center" style="width: 20%">Valor unitario</th>
                                <th class="text-center" style="width: 20%">Cantidad disponible</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalComentarios" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Comentarios</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Lista</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed table-hover table-stripped" style="font-size:small">
                            <thead>
                                <tr>
                                    <th width="25%" class="text-center">Usuario</th>
                                    <th width="50%" class="text-center">Comentario</th>
                                    <th width="25%" class="text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyComentarios">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box box-solid last">
                    <div class="box-header with-border">
                        <h3 class="box-title">Nuevo comentario</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <textarea placeholder="Ingrese un comentario" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnRegistrarComentario" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <link href="<?php echo e(asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')); ?>" rel="stylesheet" type="text/css" />
    <script src="<?php echo e(asset('assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js')); ?>"></script>

    <link href="<?php echo e(asset('assets/datatables/css/dataTables.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css" />
    <script src="<?php echo e(asset('assets/datatables/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/datatables/js/dataTables.bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js')); ?>"></script>

    <link href="<?php echo e(asset('assets/bootstrap-select/css/bootstrap-select.min.css')); ?>" rel="stylesheet" type="text/css" />
    <script src="<?php echo e(asset('assets/bootstrap-select/js/bootstrap-select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js')); ?>"></script>

    <link href="<?php echo e(asset('assets/lobibox/dist/css/lobibox.min.css')); ?>" rel="stylesheet" type="text/css" />
    <script src="<?php echo e(asset('assets/lobibox/dist/js/lobibox.min.js')); ?>"></script>

    <script src="<?php echo e(asset('assets/loadingoverlay/loadingoverlay.min.js')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/util.js?v=27')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/producto/producto-model.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/producto/producto-view.js?v=11')); ?>"></script>

    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/descarga/proforma/descarga-proforma-view.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/descarga/proforma/descarga-proforma-model.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/producto/historial-model.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/producto/historial-view.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=21')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=21')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/entidad/contacto-model.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/entidad/contacto-view.js?v=11')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/cuadro-costos/fondo-proveedor-view.js?v=13')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/cuadro-costos/fondo-proveedor-model.js?v=13')); ?>"></script>

    <script type="module">
        import COVistaAnteriorModel from "<?php echo e(asset('mgcp/js/acuerdo-marco/proforma/individual/compra-ordinaria/co-vista-anterior-model.js?v=23')); ?>";
        import COVistaAnteriorView from "<?php echo e(asset('mgcp/js/acuerdo-marco/proforma/individual/compra-ordinaria/co-vista-anterior-view.js?v=23')); ?>";

        import ProformaIndividualModel from "<?php echo e(asset('mgcp/js/acuerdo-marco/proforma/individual/proforma-individual-model.js?v=23')); ?>";
        import GCVistaAnteriorView from "<?php echo e(asset('mgcp/js/acuerdo-marco/proforma/individual/gran-compra/gc-vista-anterior-view.js?v=23')); ?>";

        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            Util.activarDatePicker();
            $(".sidebar-mini").addClass("sidebar-collapse");

            const token = '<?php echo e(csrf_token()); ?>';
            const tipoProforma = '<?php echo e($tipoProforma); ?>';
            const entidadView = new EntidadView(new EntidadModel(token));
            const contactoView = new ContactoEntidadView(new ContactoEntidadModel(token), '<?php echo e(Auth::user()->tieneRol(60)); ?>');
            const ocPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
            const historialView = new HistorialProductoView(new HistorialProductoModel(token));

            const productoView = new ProductoView(new ProductoModel(token));
            const descargaProformaView = new DescargaProformaView(new DescargaProformaModel(token));
            const fondo = new FondoProveedorView(new FondoProveedorModel(token));

            let proformaView;
            if (tipoProforma == '1') {
                proformaView = new COVistaAnteriorView(new COVistaAnteriorModel(token), "<?php echo e(Auth::user()->id); ?>");
                proformaView.ingresarFletePorLoteEvent();
            } else {
                proformaView = new GCVistaAnteriorView(new ProformaIndividualModel(token), "<?php echo e(Auth::user()->id); ?>");
            }
            proformaView.listarProformas("<?php echo e(Auth::user()->tieneRol(44)); ?>", "<?php echo e(Auth::user()->tieneRol(123)); ?>");
            proformaView.enviarCotizacionesEvent();
            proformaView.deshacerCotizacionEvent();
            proformaView.actualizarCamposEvent();

            Util.activarFiltros('#tableProformas');

            historialView.obtenerHistorialEvent();
            ocPublicaView.verOfertasPorMMNEvent();
            entidadView.obtenerDetallesEvent();
            productoView.obtenerPrecioStockPortalEvent();
            productoView.obtenerDetallesEvent();
            descargaProformaView.obtenerFechasUltimaDescargaEvent();
            fondo.obtenerFondosParaProformaEvent();
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/acuerdo-marco/proforma/individual/vista-anterior.blade.php ENDPATH**/ ?>