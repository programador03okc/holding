<?php $__env->startSection('contenido'); ?>

<?php $__env->startSection('cabecera'); ?>
Publicar stock por empresa
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Publicar</li>
    <li class="active">Nuevos precios</li>
</ol>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('cuerpo'); ?>
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Configuración</h3>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-1 col-sm-offset-2 control-label">Empresa:</label>
                <div class="col-sm-3">
                    <select class="form-control" id="selectEmpresa" name="empresa">
                        <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->empresa); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <a id="aSubirArchivos" href="#">Subir archivos de stocks</a>
                </div>

                <div class="col-sm-1">
                    <button id="btnIniciar" class="btn btn-primary">Iniciar</button>
                </div>
                <label class="col-sm-1 control-label">Resultado:</label>
                <div class="col-sm-2">
                    <div class="form-control-static" id="divProgreso">En espera</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Resultado</h3>
    </div>
    <div class="box-body">
        <table style="width: 100%" class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 10%">N°</th>
                    <th class="text-center" style="width: 10%">ID</th>
                    <th class="text-center">Producto</th>
                    <th class="text-center" style="width: 10%">Stock</th>
                    <th class="text-center" style="width: 10%">Resultado</th>
                </tr>
            </thead>
            <tbody id="tbodyProductos">

            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalSubirArchivos" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Subir archivos de stocks</h4>
            </div>
            <div class="modal-body">
                <form id="formSubirArchivos" method="post" action="<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-empresa.procesar-archivo')); ?>" class="form-horizontal">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">Empresa</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="empresa">
                                <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->empresa); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ruc" class="col-sm-3 control-label">Archivo</label>
                        <div class="col-sm-8">
                            <input style="margin-bottom: 10px" type="file" class="form-control" name="archivo">
                            <a href="<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-empresa.descargar-plantilla')); ?>" target="_blank">Descargar plantilla</a>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 mensaje">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnSubirArchivo" class="btn btn-primary">Subir</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src='<?php echo e(asset("mgcp/js/util.js?v=25")); ?>'></script>
<script>
    $(document).ready(function() {
        Util.seleccionarMenu(window.location);
        let filaActual = 0;
        let totalFilas = 0;
        let $progreso = $('#divProgreso');
        let $tbodyProductos = $('#tbodyProductos');
        let $selectEmpresa = $('#selectEmpresa');
        let $botonIniciar = $('#btnIniciar');

        $('#aSubirArchivos').click(function(e) {
            e.preventDefault();
            $('#modalSubirArchivos').modal('show');
        });

        $('#btnSubirArchivo').click(function() {
            $('#formSubirArchivos').submit();
        });

        $("#formSubirArchivos").on("submit", function(e) {
            e.preventDefault();
            let $boton = $('#btnSubirArchivo');
            $boton.prop('disabled', true);
            let formData = new FormData(document.getElementById("formSubirArchivos"));
            let $modal = $('#modalSubirArchivos');
            Util.mensaje($modal.find('div.mensaje'), 'info', 'Procesando archivo...', false);
            $.ajax({
                url: $("#formSubirArchivos").attr('action'),
                type: "post",
                dataType: "json",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    Util.mensaje($modal.find('div.mensaje'), data.tipo, data.mensaje);
                },
                error: function() {
                    Util.mensaje($modal.find('div.mensaje'), 'danger', 'Hubo un problema al subir el archivo. Por favor actualice la página e intente de nuevo');
                },
                complete: function() {
                    $boton.prop('disabled', false);
                }
            });
        });

        $('#btnIniciar').click(function() {
            filaActual = 0;
            totalFilas = 0;
            $selectEmpresa.prop('disabled', true);
            $botonIniciar.prop('disabled', true);
            $progreso.html('Obteniendo productos...');
            $.ajax({
                url: "<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-empresa.obtener-productos')); ?>",
                type: 'post',
                //dataType: 'json',
                data: {
                    idEmpresa: $selectEmpresa.val(),
                    _token: "<?php echo e(csrf_token()); ?>"
                },
                success: function(datos) {
                    $tbodyProductos.html(datos);
                    totalFilas = $tbodyProductos.find('tr').length;
                    if (totalFilas == 1 && $tbodyProductos.find('td:eq(0)').html().includes('Sin productos')) {
                        $progreso.html('<span class="text-danger">Sin productos</span>');
                        $botonIniciar.prop('disabled', false);
                    } else {
                        procesar();
                    }
                },
                error: function() {
                    $selectEmpresa.prop('disabled', false);
                    $botonIniciar.prop('disabled', false);
                    $progreso.html('<span class="text-danger">Error al obtener productos. Por favor inténtelo de nuevo.</span>');
                }
            });
        });

        function procesar() {
            if (filaActual < totalFilas) {
                $progreso.html('Procesando ' + (filaActual + 1) + ' de ' + totalFilas);
                let $fila = $tbodyProductos.find('tr:eq(' + filaActual + ')');
                $fila.find('td.resultado').html('Procesando...');
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-empresa.enviar-portal')); ?>",
                    type: 'post',
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        idProducto: $fila.find('td.id-producto').html(),
                        stock: $fila.find('td.stock').html(),
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(datos) {
                        $fila.find('td.resultado').html(`<span class="text-${datos.tipo}">${datos.mensaje}</span>`);
                        filaActual++;
                    },
                    error: function() {
                        $fila.find('td.resultado').html('<span class="text-danger">Error del servidor</span>');
                    },
                    complete: function() {
                        procesar();
                    }
                });
            } else {
                $progreso.html('<span class="text-success">Fin de publicación</span>');
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/acuerdo-marco/publicar/stock-empresa.blade.php ENDPATH**/ ?>