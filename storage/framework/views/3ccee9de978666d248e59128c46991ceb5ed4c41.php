
<?php $__env->startSection('estilos'); ?>
    <link href="<?php echo e(asset('assets/datatables/css/dataTables.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/bootstrap-select/css/bootstrap-select.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/lobibox/dist/css/lobibox.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        .sin-borde-top tr td,
        .sin-borde-top tr th {
            border-top: none !important;
        }
        #tableOportunidades td a:hover {
            cursor: pointer
        }
        /*div.modal legend {
            font-size: 16px;
            font-weight: bold;
        }*/
        .arriba {
            margin-bottom: 3px;
        }
        ::placeholder {
            color: black;
            opacity: 0.4;
        }
        #modalDetallesPc table td {
            font-size: small !important;
        }
        input.upper {
            text-transform: uppercase;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contenido'); ?>
<?php
    use App\Models\mgcp\AcuerdoMarco\Producto\Catalogo;
?>
<?php $__env->startSection('cabecera'); ?> Lista de órdenes de compra propias <?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">O/C propias</li>
    <li class="active">Lista</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<?php echo $__env->make('mgcp.partials.acuerdo-marco.entidad.detalles',['seleccionarContacto' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableOrdenes" class="table table-striped table-hover table-condensed" style="font-size: 0.8em; width: 100%">
                <thead>
                    <tr>
                        <th class="text-center">N°</th>
                        <th class="text-center">AM</th>
                        <th style="width:15%" class="text-center">Entidad</th>
                        <th class="text-center">Fecha<br>publicación</th>
                        <th class="text-center">Estado O/C</th>
                        <th class="text-center">Fecha<br>estado</th>
                        <th class="text-center">Estado entrega</th>
                        <th class="text-center">Inicio/fin<br>entrega</th>
                        <th class="text-center">Monto total</th>
                        <th style="width:5%" class="text-center">O.C. (fís.)/<br>SIAF</th>
                        <!--<th width="5%" class="text-center">Cód. gasto /<br>Factura</th>-->
                        <th style="width:6%" class="text-center">Factura /<br>OCC(Softlink)</th>
                        <th class="text-center">Guía /<br>Fecha guía</th>
                        <th title="Etapa adquisición" class="text-center">Etapa adq.</th>
                        <th class="text-center">Responsable</th>
                        <th style="width:9%" class="text-center">CP, estado y F.aprob.</th>
                        <th style="width:5%" class="text-center">Acciones</th>
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
                                <label title="Fecha de publicación">
                                    <input type="checkbox" name="chkFechaPublicacion" <?php if(session('ocFiltroFechaPublicacionDesde')!==null): ?> checked <?php endif; ?>> Fecha publicación
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaPublicacionDesde" class="form-control date-picker" value="<?php if(session('ocFiltroFechaPublicacionDesde')!==null): ?><?php echo e(session('ocFiltroFechaPublicacionDesde')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaPublicacionHasta" class="form-control date-picker" value="<?php if(session('ocFiltroFechaPublicacionHasta')!==null): ?><?php echo e(session('ocFiltroFechaPublicacionHasta')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label title="Fecha estado">
                                    <input type="checkbox" name="chkFechaEstado" <?php if(session('ocFiltroFechaEstadoDesde')!==null): ?> checked <?php endif; ?>> Fecha estado
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEstadoDesde" class="form-control date-picker" value="<?php if(session('ocFiltroFechaEstadoDesde')!==null): ?><?php echo e(session('ocFiltroFechaEstadoDesde')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEstadoHasta" class="form-control date-picker" value="<?php if(session('ocFiltroFechaEstadoHasta')!==null): ?><?php echo e(session('ocFiltroFechaEstadoHasta')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label title="Fecha entrega">
                                    <input type="checkbox" name="chkFechaEntrega" <?php if(session('ocFiltroFechaEntregaDesde')!==null): ?> checked <?php endif; ?>> Fecha entrega
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEntregaDesde" class="form-control date-picker" value="<?php if(session('ocFiltroFechaEntregaDesde')!==null): ?><?php echo e(session('ocFiltroFechaEntregaDesde')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaEntregaHasta" class="form-control date-picker" value="<?php if(session('ocFiltroFechaEntregaHasta')!==null): ?><?php echo e(session('ocFiltroFechaEntregaHasta')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEmpresa" <?php if(session('ocFiltroEmpresa')!==null): ?> checked <?php endif; ?>> Empresas
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEmpresa[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="5">
                                <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('ocFiltroEmpresa')): ?>
                                    <?php if(in_array($empresa->id,session('ocFiltroEmpresa'))): ?> selected <?php endif; ?> <?php endif; ?>
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
                                    <input type="checkbox" name="chkMarca" <?php if(session('ocFiltroMarca')!==null): ?> checked <?php endif; ?>> Marcas
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectMarca[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="5">
                                <?php $__currentLoopData = $marcas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('ocFiltroMarca')): ?>
                                    <?php if(in_array($marca->marca,session('ocFiltroMarca'))): ?> selected <?php endif; ?> <?php endif; ?>
                                    value="<?php echo e($marca->marca); ?>"><?php echo e($marca->marca); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEntidad" <?php if(session('ocFiltroEntidad')!==null): ?> checked <?php endif; ?>> Entidad
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="selectEntidad" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="false" data-size="10">
                                <?php $__currentLoopData = $entidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($entidad->id); ?>" <?php if(session('ocFiltroEntidad')==$entidad->id): ?> selected <?php endif; ?>><?php echo e($entidad->nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small style="margin-bottom:0px" class="help-block">Sólo se muestran las entidades que emitieron O/C a las empresas</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstadoOc" <?php if(session('ocFiltroEstadoOc')!==null): ?> checked <?php endif; ?>> Estado O/C
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="estadoOc[]" class="selectpicker" data-live-search="true" data-width="100%" multiple data-size="5">
                                <?php $__currentLoopData = $estadosOc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('ocFiltroEstadoOc')): ?>
                                    <?php if(in_array($estado->estado_oc,session('ocFiltroEstadoOc'))): ?> selected <?php endif; ?> <?php endif; ?>
                                    value="<?php echo e($estado->estado_oc); ?>"><?php echo e($estado->estado_oc); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstadoCuadro" <?php if(session('ocFiltroEstadoCuadro') !== null): ?> checked <?php endif; ?>> Estado cuadro
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="estadoCuadro">
                                <?php $__currentLoopData = $estadosCuadro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($estado->id); ?>" <?php if(session('ocFiltroEstadoCuadro') == $estado->id): ?> selected <?php endif; ?>><?php echo e($estado->estado); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkEstadoEntrega" <?php if(session('ocFiltroEstadoEntrega') !== null): ?> checked <?php endif; ?>> Estado entrega
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="estadoEntrega">
                                <?php $__currentLoopData = $estadosEntrega; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($estado->estado_entrega); ?>" <?php if(session('ocFiltroEstadoEntrega') == $estado->estado_entrega): ?> selected <?php endif; ?>><?php echo e($estado->estado_entrega); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkAm" <?php if(session('ocFiltroAm') !== null): ?> checked <?php endif; ?>> Acuerdo marco
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select name="acuedoMarco[]" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="10">
                                <?php $__currentLoopData = $acuerdos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acuerdo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php if(session()->has('ocFiltroAm')): ?>
                                    <?php if(in_array($acuerdo->id,session('ocFiltroAm'))): ?> selected <?php endif; ?> <?php endif; ?>
                                    value="<?php echo e($acuerdo->id); ?>"><?php echo e($acuerdo->descripcion); ?> - <?php echo e($acuerdo->descripcion_larga); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkTipo" <?php if(session('ocFiltroTipo')!==null): ?> checked <?php endif; ?>> Tipo de O/C
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="tipoOc">
                                <option value="0" <?php if(session('ocFiltroTipo')==0): ?> selected <?php endif; ?>>Directa</option>
                                <option value="1" <?php if(session('ocFiltroTipo')==1): ?> selected <?php endif; ?>>Compra ordinaria</option>
                                <option value="2" <?php if(session('ocFiltroTipo')==2): ?> selected <?php endif; ?>>Gran compra</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label title="Etapa adquisición">
                                    <input type="checkbox" name="chkEtapaAdq" <?php if(session('ocFiltroEtapaAdq')!==null): ?> checked <?php endif; ?>> Etapa adq.
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="etapaAdq">
                                <?php $__currentLoopData = $etapas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $etapa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($etapa->id); ?>" <?php if(session('ocFiltroEtapaAdq')==$etapa->id): ?> selected <?php endif; ?>><?php echo e($etapa->etapa); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php if(Auth::user()->tieneRol(48)): ?>
                    <div class="form-group">
                        <label class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" disabled checked> Sólo ve órdenes donde es responsable
                                </label>
                            </div>
                        </label>
                    </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCorporativo" <?php if(session('ocFiltroCorporativo')!==null): ?> checked <?php endif; ?>> Corporativo
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="corporativo">
                                <?php $__currentLoopData = $corporativos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $corporativo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($corporativo->id); ?>" <?php if(session('ocFiltroCorporativo')==$corporativo->id): ?> selected <?php endif; ?>><?php echo e($corporativo->nombre_corto); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFlagEstado" <?php if(session('ocFiltroFlagEstado')!==null): ?> checked <?php endif; ?>> Flag de estados
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-8">
                            <select class="form-control" name="flagEstado">
                                <?php $__currentLoopData = $flags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($flag->color); ?>" <?php if(session('ocFiltroFlagEstado') == $flag->color): ?> selected <?php endif; ?>><?php echo e($flag->nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkSolAprob24h" <?php echo e(session('ocFiltroSolAprob24h') != null ? "checked" : ""); ?>> Con sol. aprob. después de 24h
                                </label>
                            </div>
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

<div class="modal fade" id="modalDescargarOrdenes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Descargar O/C desde Perú Compras</h4>
            </div>
            <div class="modal-body">
                <div class="mensaje-inicial"></div>
                <p>Seleccione las empresas que desee descargar. Al iniciar el proceso, puede cerrar esta ventana y la descarga continuará</p>
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-hover" style="width: 100%;font-size:small">
                        <thead>
                            <tr>
                                <!--<th class="text-center" style="width: 5%">N°</th>-->
                                <th style="width: 15%" class="text-center">Empresa</th>
                                <th style="width: 35%" class="text-center">Última descarga</th>
                                <th style="width: 15%" class="text-center">Seleccionar<br><input type="checkbox" id="chkSeleccionarTodo" checked></th>
                                <th style="width: 35%" class="text-center">Progreso</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDescargarOc">
                            <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="empresa"><?php echo e($empresa->empresa); ?></td>
                                <td class="text-center fecha"></td>
                                <td class="text-center">
                                    <input type="checkbox" checked>
                                    <?php
                                    $catalogos = Catalogo::obtenerCatalogosPorEmpresa($empresa->id);
                                    foreach ($catalogos as $catalogo) {
                                        echo '<input type="hidden" class="pendiente" data-empresa="' . $empresa->id . '" data-catalogo="' . $catalogo->id . '" value="">';
                                    }
                                    ?>
                                </td>
                                <td class="resultado text-center"></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mensaje-final"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnDescargarOrdenes">Descargar</button>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="<?php echo e(route('mgcp.ordenes-compra.propias.exportar-lista')); ?>" target="_blank">
    <div class="modal fade" id="modalExportarLista" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Exportar lista</h4>
                </div>
                <div class="modal-body">
                    <?php echo csrf_field(); ?>
                    <p>Se exportará la lista de órdenes de acuerdo a los filtros aplicados (no se toma en cuenta el criterio de búsqueda ingresado). Considere actualizar la lista (con la opción Descargar O/C desde Perú Compras) antes de exportarla</p>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="incluirProductos" value="1"> Incluir lista de productos vendidos de OCAM
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" id="btnExportarLista">Exportar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="modalEstadosOcPortal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Estados de <span class="orden-compra"></span></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallesPc" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detalles de Orden de Compra</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
                <div class="box box-solid <?php if(!Auth::user()->tieneRol(36)): ?> last <?php endif; ?>">
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
                <?php if(Auth::user()->tieneRol(36)): ?>
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
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <?php if(Auth::user()->tieneRol(36)): ?>
                <button type="button" id="btnRegistrarComentario" class="btn btn-primary">Registrar</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTransportes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Transportes para</h4>
            </div>
            <div class="modal-body">

                <div class="box box-solid <?php if(!Auth::user()->tieneRol(36)): ?> last <?php endif; ?>">
                    <div class="box-body">

                        <table class="table table-condensed" style="font-size:small">
                            <thead>
                                <tr>
                                    <th class="text-center">Fecha despacho</th>
                                    <th class="text-center">Transportista</th>
                                    <th class="text-center">Nro. guía</th>
                                    <th style="width: 10%" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTransportes">
                            </tbody>
                        </table>

                    </div>
                </div>

                <?php if(Auth::user()->tieneRol(36)): ?>
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Nuevo transporte</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div>Fecha despacho</div>
                                    <input id="txtTransporteFecha" type="text" class="form-control date-picker" placeholder="dd-mm-aaaa">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div>Transportista</div>
                                    <div id="divTransportistas" style="margin-bottom: 5px">

                                    </div>
                                    <a id="aNuevoTransportista" href="#">Nuevo transportista</a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div>Nro. guía</div>
                                    <input id="txtTransporteNroGuia" type="text" class="form-control" placeholder="Nro. guía">
                                </div>
                            </div>
                            <div class="col-md-2" style="padding-left: 5px">
                                <div class="form-group">
                                    <div style="visibility: hidden">Nro. guía</div>
                                    <button type="button" id="btnNuevoTransporte" class="btn btn-primary">Registrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalActualizarDespacho" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo e(Auth::user()->tieneRol(51) ? 'Actualizar' : 'Ver'); ?> despacho</h4>
            </div>
            <div class="modal-body">
                <p>Orden: <span class="orden"></span></p>
                <form id="formActualizarDespacho">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id">
                    <input type="hidden" name="tipo">
                    <div class="radio" style="margin-bottom: 1.5em">
                        <label>
                            <input type="radio" name="despachada" value="0">
                            No despachada
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="despachada" value="1">
                            Despachada
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Transportista</label>
                        <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" data-size="5" name="transportista">
                            <option value="0">No seleccionado</option>
                            <?php $__currentLoopData = $transportistas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transportista): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($transportista->id_contribuyente); ?>"><?php echo e($transportista->razon_social); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Flete real</label>
                        <div class="input-group">
                            <span class="input-group-addon">S/</span>
                            <input type="text" class="form-control decimal" name="fleteReal" placeholder="Flete real">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fecha de salida</label>
                        <input type="text" class="form-control date-picker" name="fechaSalida" placeholder="Fecha de salida">
                    </div>
                    <div class="form-group">
                        <label>Fecha de llegada al cliente</label>
                        <input type="text" class="form-control date-picker" name="fechaLlegada" placeholder="Fecha de llegada">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <?php if(Auth::user()->tieneRol(51)): ?>
                <button type="button" id="btnActualizarDespacho" class="btn btn-primary">Actualizar</button>
                <?php endif; ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalInformacionAdicional" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Información adicional de <span class="orden-compra"></span></h4>
            </div>
            <div class="modal-body">
                <div class="mensaje"></div>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Lugar de entrega</label>
                        <div class="col-sm-8">
                            <div class="form-control-static lugar-entrega limpiar"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Archivos</label>
                        <div class="col-sm-8">
                            <div class="form-control-static archivos limpiar"></div>
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

<div class="modal fade" id="modalCambiarContacto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cambiar contacto</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableContactosEntidadSeleccionar" class="table table-condensed table-hover table-striped" style="font-size: small">
                        <thead>
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Teléfono</th>
                                <th class="text-center">Cargo</th>
                                <th class="text-center">Correo</th>
                                <th class="text-center">Dirección</th>
                                <th class="text-center">Horario</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrearCuadroCosto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Crear cuadro de costos para la orden <span class="orden-compra"></span></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txtIdOc">
                <input type="hidden" id="txtTipoOc">
                <p>Para crear un cuadro de costo, debe vincular o crear una oportunidad para esta orden de compra</p>
                <br>
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tabVincular">Vincular oportunidad</a></li>
                    <li><a data-toggle="tab" href="#tabCrear">Crear oportunidad</a></li>
                </ul>

                <div class="tab-content">
                    <div id="tabVincular" class="tab-pane fade in active">
                        <br>
                        <!--<div>Seleccione  la oportunidad a vincular con la O/C.</div>-->
                        <table id="tableOportunidades" class="table table-hover table-striped table-condensed" style="width: 100%; font-size: x-small ;">
                            <thead>
                                <tr>
                                    <th class="text-center">Entidad</th>
                                    <th class="text-center">Oportunidad</th>
                                    <th class="text-center">Importe</th>
                                    <th class="text-center">Fecha<br>creación</th>
                                    <th class="text-center">Fecha<br>límite</th>
                                    <th class="text-center">Resp.</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Grupo</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div id="tabCrear" class="tab-pane fade">
                        <br>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <div class="col-sm-3 control-label">Descripción</div>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="txtOportunidadDescripcion" name="descripcion" placeholder="Descripción de oportunidad"></textarea>
                                </div>
                            </div>
                            <?php if(Auth::user()->tieneRol(4)): ?>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">Responsable</div>
                                <div class="col-sm-8">
                                    <select class="form-control" id="selectOportunidadResponsable">
                                        <?php $__currentLoopData = $corporativos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $corporativo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($corporativo->id); ?>"><?php echo e($corporativo->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <span class="help-block">El resto de campos se podrá editar después de crearse la oportunidad<br>
                                        El responsable de la oportunidad será responsable de esta orden de compra
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-3">
                                    <button class="btn btn-primary" id="btnCrearOportunidadDesdeOc">Crear oportunidad</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="hidden">
    <select id="selectEtapas">
        <?php $__currentLoopData = $etapas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $etapa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($etapa->id); ?>"><?php echo e($etapa->etapa); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select id="selectCorporativos">
        <option value="0">No asignado</option>
        <?php $__currentLoopData = $corporativos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $corporativo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($corporativo->id); ?>"><?php echo e($corporativo->nombre_corto); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select id="selectUsuarios">
        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($usuario->id); ?>"><?php echo e($usuario->nombre_corto); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
    
    <script src='<?php echo e(asset("assets/datatables/js/jquery.dataTables.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/js/dataTables.bootstrap.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js")); ?>'></script>

    <script src="<?php echo e(asset('assets/bootstrap-select/js/bootstrap-select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js')); ?>"></script>

    <script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js")); ?>'></script>

    <script src='<?php echo e(asset("assets/jquery-number/jquery.number.min.js")); ?>'></script>
    <script src="<?php echo e(asset('assets/lobibox/dist/js/lobibox.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/loadingoverlay/loadingoverlay.min.js')); ?>"></script>

    <script src='<?php echo e(asset("mgcp/js/util.js?v=27")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/moment.min.js?v=1")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=13")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=13")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/contacto-model.js?v=13")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/contacto-view.js?v=14")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/orden-compra-propia-view.js")); ?>?v=<?php echo e(filemtime(public_path("mgcp/js/orden-compra/propia/orden-compra-propia-view.js"))); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/orden-compra-propia-model.js?v=20")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/comentario-oc-view.js?v=12")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/comentario-oc-model.js?v=12")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=11")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=11")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-model.js?v=11")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/oportunidad/oportunidad-view.js?v=11")); ?>'></script>

    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/despacho-view.js?v=10")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/despacho-model.js?v=10")); ?>'></script>
    <script>
        $(document).ready(function() {
            //Util.activarDatePicker();
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            $(".sidebar-mini").addClass("sidebar-collapse");
            
            //var contenido = '';
            const permisos = {
                editarGuiaFecha: "<?php echo e(Auth::user()->tieneRol(32)); ?>",
                editarCodGastoFactura: "<?php echo e(Auth::user()->tieneRol(33)); ?>",
                editarEtapaAdq: "<?php echo e(Auth::user()->tieneRol(34)); ?>",
                editarCobrado: "<?php echo e(Auth::user()->tieneRol(35)); ?>",
                editarOtros: "<?php echo e(Auth::user()->tieneRol(47)); ?>",
                crearCuadro: "<?php echo e(Auth::user()->tieneRol(50)); ?>",
            };

            const token = '<?php echo e(csrf_token()); ?>';
            const entidadView = new EntidadView(new EntidadModel(token));
            const contactoView = new ContactoEntidadView(new ContactoEntidadModel(token), '<?php echo e(Auth::user()->tieneRol(60)); ?>', true);
            const comentarioView = new ComentarioOcView(new ComentarioOcModel(token));
            const ordenCompraPropiaView = new OrdenCompraPropiaView(new OrdenCompraPropiaModel(token), permisos, '<?php echo e(Auth::user()->id); ?>');
            const ordenCompraPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
            const oportunidadView = new OportunidadView(new OportunidadModel(token));
            const despachoView = new DespachoView(new DespachoModel(token), '<?php echo e(Auth::user()->tieneRol(51)); ?>');

            entidadView.obtenerDetallesEvent();
            comentarioView.listarComentariosEvent();
            comentarioView.registrarComentarioEvent();
            ordenCompraPublicaView.obtenerEstadosPortalEvent();
            ordenCompraPropiaView.actualizarCamposEvent();
            ordenCompraPropiaView.crearCuadroCostoEvent();
            ordenCompraPropiaView.descargarDesdePortalEvent();
            oportunidadView.listarParaOc();
            oportunidadView.crearOportunidadDesdeOcEvent(ordenCompraPropiaView.rutaCuadroCosto);
            ordenCompraPropiaView.listar();
            ordenCompraPropiaView.informacionAdicionalEvent();
            ordenCompraPropiaView.cambiarContactoEvent();
            ordenCompraPropiaView.verProductosEvent();
            despachoView.obtenerDetallesEvent();
            despachoView.actualizarDespachoEvent();
            Util.activarFiltros('#tableOrdenes', ordenCompraPropiaView.model);
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/orden-compra/propia/lista.blade.php ENDPATH**/ ?>