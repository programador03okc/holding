
<?php $__env->startSection('estilos'); ?>
<link href="<?php echo e(asset('assets/datatables/css/dataTables.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo e(asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css"/>

<link href="<?php echo e(asset('assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')); ?>" rel="stylesheet" type="text/css"/>
<style>

</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contenido'); ?>


<?php $__env->startSection('cabecera'); ?>
Órdenes de compra públicas
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Órdenes de compra</li>
    <li class="active">O/C públicas</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.actualizar-stock-precio', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.historial-actualizaciones', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.detalles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.entidad.detalles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableOrdenes" class="table table-hover table-condensed table-striped" style="font-size: small; width:100%">
                <thead>
                    <tr>
                        <th style="width:10%" class="text-center">O/C</th>
                        <th style="width:7%" class="text-center">Fecha</th>
                        <th style="width:15%" class="text-center">Entidad</th>
                        <th style="width:15%" class="text-center">Proveedor</th>
                        <th style="width:9%" class="text-center">Categoría</th>
                        <th style="width:10%" class="text-center">Producto</th>
                        <th style="width:8%" class="text-center">Nro Parte</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Precio soles</th>
                        <th class="text-center">Precio dólares</th>
                        <th class="text-center">Costo envío</th>
                        <th style="width:6%" class="text-center">Días entrega</th>
                        <th style="width:6%" class="text-center">Lugar entrega</th>
                        <th style="width:8%" class="text-center">Herram.</th>
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
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkOdenesProducto" <?php if(session('oc_ordenes_producto')!==null): ?> checked <?php endif; ?>> Órdenes con productos que tengo publicados
                                </label>
                            </div>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkOrdenesFecha" <?php if(session('oc_ordenes_fecha')!==null): ?> checked <?php endif; ?>> Órdenes con fecha
                                </label>
                            </div>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkFecha" <?php if(session('oc_filtro_fecha')!==null): ?> checked <?php endif; ?>> Fecha
                                </label>
                            </div>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" name="fechaDesde" class="form-control date-picker" value="<?php if(session('oc_fecha_desde')!==null): ?><?php echo e(session('oc_fecha_desde')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Desde (dd-mm-aaaa)</small>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="fechaHasta" class="form-control date-picker" value="<?php if(session('oc_fecha_hasta')!==null): ?><?php echo e(session('oc_fecha_hasta')); ?><?php else: ?><?php echo e(date('d-m-Y')); ?><?php endif; ?>">
                            <small class="help-block">Hasta (dd-mm-aaaa)</small>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js")); ?>"></script>

<script src="<?php echo e(asset("assets/datatables/js/jquery.dataTables.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/datatables/js/dataTables.bootstrap.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js")); ?>"></script>
<link href="<?php echo e(asset('assets/lobibox/dist/css/lobibox.min.css')); ?>" rel="stylesheet" type="text/css" />
<script src="<?php echo e(asset("assets/lobibox/dist/js/lobibox.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/loadingoverlay/loadingoverlay.min.js")); ?>"></script>
<script src="<?php echo e(asset("mgcp/js/util.js?v=11")); ?>"></script>
<script src='<?php echo e(asset("mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=13")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=13")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/entidad-view.js?v=12")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/entidad-model.js?v=12")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/contacto-model.js?v=12")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/entidad/contacto-view.js?v=12")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/historial-model.js?v=11")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/historial-view.js?v=11")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=11")); ?>'></script>
<script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/producto-view.js?v=11")); ?>'></script>
<script>
    $(document).ready(function () {
        Util.activarDatePicker();
        Util.seleccionarMenu(window.location);
        const token = '<?php echo e(csrf_token()); ?>';
        const entidadView = new EntidadView(new EntidadModel(token));
        const contactoView = new ContactoEntidadView(new ContactoEntidadModel(token),'<?php echo e(Auth::user()->tieneRol(60)); ?>');
        const ocPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
        const historialView = new HistorialProductoView(new HistorialProductoModel(token));
        const productoView = new ProductoView(new ProductoModel(token));

        historialView.obtenerHistorialEvent();
        entidadView.obtenerDetallesEvent();
        productoView.obtenerPrecioStockPortalEvent();
        productoView.obtenerDetallesEvent();
        ocPublicaView.listar();
        Util.activarFiltros('#tableOrdenes', ocPublicaView.model);
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/orden-compra/publica/lista.blade.php ENDPATH**/ ?>