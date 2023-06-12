
<?php $__env->startSection('estilos'); ?>
    <link href='<?php echo e(asset("assets/datatables/css/dataTables.bootstrap.min.css")); ?>' rel="stylesheet" type="text/css" />
    <link href='<?php echo e(asset("assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css")); ?>' rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/lobibox/dist/css/lobibox.min.css')); ?>" rel="stylesheet" type="text/css" />
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
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cabecera'); ?> Lista de productos <?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Productos</li>
    <li class="active">Lista</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.actualizar-stock-precio', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.orden-compra.publica.ofertas-por-producto', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('mgcp.partials.acuerdo-marco.producto.historial-actualizaciones', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableProductos" class="table table-condensed table-hover table-striped" style="font-size: 11px; width: 100%">
                <thead>
                    <tr>
                        <th class="text-center">Acuerdo</th>
                        <th class="text-center">Producto</th>
                        <th style="width: 10%" class="text-center">Marca</th>
                        <th style="width: 10%" class="text-center">Modelo</th>
                        <th style="width: 10%" class="text-center">Nro. parte</th>
                        <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="text-center"><?php echo e($empresa->empresa); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="text-center" style="width: 10%">Herramientas</th>
                        <th>Desc</th>
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
                    <?php echo e(csrf_field()); ?>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="fom-control-static mensaje-filtros">Seleccione los filtros que desee aplicar y
                                cierre este cuadro para continuar</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4 form-control-static">
                            Sólo los catálogos:
                        </div>
                        <div class="col-sm-8">
                            <?php $__currentLoopData = $catalogos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catalogo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkCatalogo[]" value="<?php echo e($catalogo->id); ?>" <?php if(session()->has('prod_catalogos') && in_array($catalogo->id,session('prod_catalogos'))): ?> checked <?php endif; ?>> 
                                    <?php echo e($catalogo->descripcion_catalogo); ?>

                                    <small class="help-block" style="display: inline">(<?php echo e($catalogo->descripcion_am); ?>)</small>
                                </label>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="chkAdjudicados" <?php if(session('prod_adjudicados') !==null): ?> checked <?php endif; ?>> Sólo productos adjudicados en al menos una empresa
                                </label>
                            </div>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOfertasOc" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ver ofertas en órdenes de compra públicas</h4>
            </div>
            <div class="modal-body">
                <p><strong>Producto:</strong> <span class="producto"></span></p>

                <div id="divContenedorOc">
                    <table class="table table-condensed table-striped table-hover" style="font-size: x-small; width: 100%;" id="tableOrdenesCompra">
                        <thead>
                            <tr>
                                <th class="text-center">Fecha</th>
                                <th style="width: 20%" class="text-center">Proveedor</th>
                                <th class="text-center">Entidad</th>
                                <th class="text-center">Entrega</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Precio unit.</th>
                                <th class="text-center">Costo envío</th>
                                <th style="width: 5%" class="text-center">Días entrega</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyOfertasOc">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnOcultarOcSinFecha">Ocultar órdenes sin
                    fecha</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src='<?php echo e(asset("assets/datatables/js/jquery.dataTables.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/js/dataTables.bootstrap.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js")); ?>'></script>
    <script src="<?php echo e(asset('assets/loadingoverlay/loadingoverlay.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/lobibox/dist/js/lobibox.min.js')); ?>"></script>

    <script src='<?php echo e(asset("mgcp/js/util.js?v=11")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/producto-model.js?v=10")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/producto-view.js?v=16")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/historial-model.js?v=10")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/acuerdo-marco/producto/historial-view.js?v=12")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/publica/orden-compra-publica-model.js?v=11")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/publica/orden-compra-publica-view.js?v=11")); ?>'></script>
    <script>
        $(document).ready(function() {
            //*****INICIALIZACION*****
            Util.seleccionarMenu(window.location);
            Util.activarSoloDecimales();
            $(".sidebar-mini").addClass("sidebar-collapse");

            const token = '<?php echo e(csrf_token()); ?>';
            const ocPublicaView = new OrdenCompraPublicaView(new OrdenCompraPublicaModel(token));
            const historialView = new HistorialProductoView(new HistorialProductoModel(token));
            const productoView = new ProductoView(new ProductoModel(token));
            productoView.listar();
            productoView.obtenerPrecioStockPortalEvent();
            productoView.estadoProductoStock();
            ocPublicaView.verOfertasPorMMNEvent();
            historialView.obtenerHistorialEvent();
            Util.activarFiltros('#tableProductos', productoView.model);

        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/acuerdo-marco/producto/lista.blade.php ENDPATH**/ ?>