
<?php $__env->startSection('estilos'); ?>
    <style>
        table img {
            display: none;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cabecera'); ?> Descarga de productos adjudicados <?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Descargar</li>
    <li class="active">Productos adjudicados</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Configuración</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-1 control-label">Empresa:</label>
                        <div class="col-sm-2">
                            <select class="form-control" id="selectEmpresa" name="empresa">
                                <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->empresa); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button id="btnObtenerAcuerdos" class="btn btn-default">Obtener acuerdos</button>
                        </div>
                        <label class="col-sm-1 control-label">Acuerdo:</label>
                        <div class="col-sm-4" id="tdAcuerdos">
                            <p class="form-control-static">En espera</p>
                        </div>
                        <div class="col-sm-2">
                            <button id="btnIniciar" disabled class="btn btn-primary">Iniciar</button> 
                            <button id="btnSiguiente" class="btn btn-default">Siguiente</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Progreso</h3>
    </div>
    <div class="box-body">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th class="text-center">Catálogos</th>
                    <th class="text-center">Categorías</th>
                    <th class="text-center">Catálogo actual</th>
                    <th class="text-center">Categoría actual</th>
                    <th class="text-center">Productos procesados</th>
                    <th class="text-center">Progreso total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="tdCatalogos" style="width: 20%"></td>
                    <td id="tdCategorias" style="width: 20%"></td>
                    <td id="tdCatalogoActual" class="text-center"></td>
                    <td id="tdCategoriaActual" class="text-center"></td>
                    <td id="tdProductosProcesados"></td>
                    <td id="tdProgresoTotal"></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>


<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Data</h3>
    </div>
    <div class="box-body">
        <div class="col-md-12" id="divProductos">

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src='<?php echo e(asset("mgcp/js/util.js")); ?>'></script>
    <script>
        $(document).ready(function () {

            Util.seleccionarMenu(window.location);

            var totalCatalogos;
            var totalCategorias;
            var filaActual;
            var totalFilas;
            var indiceCatalogo;
            var indiceCategoria;

            var $divProductos = $('#divProductos');
            var $tbodyProductos;
            var $tdCatalogos = $('#tdCatalogos');
            var $tdCatalogoActual = $('#tdCatalogoActual');
            var $tdCategorias = $('#tdCategorias');
            var $tdCategoriaActual = $('#tdCategoriaActual');
            var $tdProductosProcesados = $('#tdProductosProcesados');
            var $tdProgresoTotal = $('#tdProgresoTotal');
            var $botonObtenerAcuerdos = $('#btnObtenerAcuerdos');
            var $botonIniciar = $('#btnIniciar');
            var $tdAcuerdos = $('#tdAcuerdos');
            var $selectEmpresa = $('#selectEmpresa');
            //var $selectTipo = $('#selectTipo');
            var $selectAcuerdos;
            var $selectCatalogos;
            var $selectCategorias;

            $('#btnSiguiente').on('click',()=>{
                filaActual=totalFilas;
            })

            $botonObtenerAcuerdos.click(function () {
                $tdAcuerdos.html('<div class="form-control-static">Obteniendo datos...</div>');
                $botonObtenerAcuerdos.prop('disabled', true);
                $botonIniciar.prop('disabled', true);
                $selectEmpresa.prop('disabled', true);
                //$selectTipo.prop('disabled', true);
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.peru-compras.obtener-acuerdos')); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {idEmpresa: $selectEmpresa.val(), pagina: 'productos', _token: "<?php echo e(csrf_token()); ?>"},
                    success: function (resultado) {
                        if (resultado.tipo == 'success')
                        {
                            if (resultado.data.length > 0)
                            {
                                var select = '<select class="form-control" id="selectAcuerdos">';
                                for (var indice in resultado.data) {
                                    select += '<option value="' + resultado.data[indice].id + '">' + resultado.data[indice].descripcion + '</option>';
                                }
                                select += '</select>';
                                $tdAcuerdos.html(select);
                                $selectAcuerdos = $('#selectAcuerdos');
                                $botonIniciar.prop('disabled', false);
                            } else
                            {
                                $tdAcuerdos.html('<span class="text-danger">Sin acuerdos. Operación no puede continuar.</span>');
                            }
                        } else
                        {
                            $tdAcuerdos.html('<span class="text-danger">' + resultado.mensaje + '</span>');
                        }

                    },
                    error: function () {
                        $tdAcuerdos.html('<div class="form-control-static"><span class="text-danger">Error al obtener acuerdos. Por favor inténtelo de nuevo.</span></div>');
                    },
                    complete: function () {
                        $botonObtenerAcuerdos.prop('disabled', false);
                        $botonIniciar.prop('disabled', false);
                        $selectEmpresa.prop('disabled', false);
                        //$selectTipo.prop('disabled', false);
                    }
                });
            });

            $botonIniciar.click(function () {
                indiceCatalogo = 0;
                indiceCategoria = 0;
                $selectEmpresa.prop('disabled', true);
                $botonIniciar.prop('disabled', true);
                $botonObtenerAcuerdos.prop('disabled', true);
                $selectAcuerdos.prop('disabled', true);
                //$selectTipo.prop('disabled', true);
                obtenerCatalogos();
                $tdCatalogoActual.html('Por favor espere...');
                $tdCategorias.html('Por favor espere...');
                $tdCategoriaActual.html('Por favor espere...');
                $tdProductosProcesados.html('Por favor espere...');
                $tdProgresoTotal.html('Por favor espere...');
            });

            function obtenerCatalogos() {
                $tdCatalogos.html('Obteniendo datos...');
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.peru-compras.obtener-catalogos')); ?>",
                    type: 'post',
                    data: {idEmpresa: $selectEmpresa.val(), idAcuerdo: $selectAcuerdos.val(), _token: "<?php echo e(csrf_token()); ?>"},
                    success: function (resultado) {
                        if (resultado.tipo == 'success')
                        {
                            if (resultado.data.length > 0)
                            {
                                var cadena = '<select class="form-control input-sm" id="selectCatalogos">';
                                for (var indice in resultado.data) {
                                    cadena += '<option value="' + resultado.data[indice].Value + '">' + resultado.data[indice].Text + '</option>';//datos[indice].descripcion + ', ';
                                }
                                cadena += '</select>';
                                $tdCatalogos.html(cadena);
                                $selectCatalogos = $('#selectCatalogos');
                                totalCatalogos = $selectCatalogos.find('option').length;
                                obtenerCategorias();
                            } else
                            {
                                $tdCatalogos.html('<span class="text-danger">Sin catálogos. Operación no puede continuar.</span>');
                            }
                        } else
                        {
                            $tdCatalogos.html('<span class="text-danger">' + resultado.mensaje + '</span>');
                        }
                    },
                    error: function () {
                        $tdCatalogos.html('<div class="form-control-static"><span class="text-danger">Error al obtener catálogos. Reintentando...</span></div>');
                        obtenerCatalogos();
                    }
                });
            }

            function obtenerCategorias() {
                $tdCatalogoActual.html($selectCatalogos.find('option:eq(' + indiceCatalogo + ')').html() + ' (' + (indiceCatalogo + 1) + ' de ' + totalCatalogos + ')');
                $tdCategorias.html('Obteniendo datos...');
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.peru-compras.obtener-categorias')); ?>",
                    type: 'post',
                    data: {idEmpresa: $selectEmpresa.val(), idCatalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(), _token: "<?php echo e(csrf_token()); ?>"},
                    success: function (resultado) {
                        if (resultado.tipo == 'success')
                        {
                            indiceCategoria = 0;
                            if (resultado.data.length > 0)
                            {
                                var cadena = '<select class="form-control input-sm" id="selectCategorias">';
                                for (var indice in resultado.data) {
                                    cadena += '<option value="' + resultado.data[indice].Value + '">' + resultado.data[indice].Text + '</option>';//datos[indice].descripcion + ', ';
                                }
                                cadena += '</select>';
                                $tdCategorias.html(cadena);
                                $selectCategorias = $('#selectCategorias');
                                totalCategorias = $selectCategorias.find('option').length;
                                obtenerProductos();
                            } else
                            {
                                $tdCategorias.html('<span class="text-danger">Sin categorías. Operación no puede continuar.</span>');
                            }
                        } else
                        {
                            $tdCategorias.html('<span class="text-danger">Error al iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo.</span>');
                        }
                    },
                    error: function () {
                        $tdCategorias.html('<div class="form-control-static"><span class="text-danger">Error al obtener catálogos. Reintentando...</span></div>');
                        obtenerCategorias();
                    }
                });
            }

            function obtenerProductos() {
                $tdCategoriaActual.html($selectCategorias.find('option:eq(' + indiceCategoria + ')').html() + ' (' + (indiceCategoria + 1) + ' de ' + totalCategorias + ')');
                $tdProductosProcesados.html('Obteniendo datos...');
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.descargar.productos-adjudicados.obtener-productos')); ?>",
                    type: 'post',
                    data: {idEmpresa: $selectEmpresa.val(),
                        idAcuerdo: $selectAcuerdos.val(),
                        idCatalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                        idCategoria: $selectCategorias.find('option:eq(' + indiceCategoria + ')').val(),
                        _token: "<?php echo e(csrf_token()); ?>"},
                    success: function (datos) {
                        $divProductos.html(datos);
                        filaActual = 0;
                        $tbodyProductos = $('#rOferta');
                        totalFilas = $tbodyProductos.find('tr').length;
                        procesarProductos();
                    },
                    error: function () {
                        $divProductos.html('<div class="text-center">Error al obtener los productos. Reintentando...</div>');
                        obtenerProductos();
                    }
                });
            }


            function procesarProductos() {
                if (filaActual < totalFilas) {
                    var $fila = $tbodyProductos.find('tr:eq(' + filaActual + ')');
                    $.ajax({
                        url: "<?php echo e(route('mgcp.acuerdo-marco.descargar.productos-adjudicados.procesar')); ?>",
                        type: 'post',
                        data: {
                            idEmpresa: $selectEmpresa.val(),
                            idAcuerdo: $selectAcuerdos.find('option:selected').val(),
                            idCatalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                            idCategoria: $selectCategorias.find('option:eq(' + indiceCategoria + ')').val(),
                            idPc: $fila.find('td:eq(0)').find('input').val(),
                            imagen: $fila.find('td:eq(0)').find('img').attr('src'),
                            descripcion: $.trim($fila.find('td:eq(1)').html()),
                            ficha: $fila.find('td:eq(2)').find('a').attr('href'),
                            moneda: $.trim($fila.find('td:eq(3)').html()),
                            precio: $.trim($fila.find('td:eq(4)').html()),
                            estado: $.trim($fila.find('td:eq(6)').html()),
                            puntaje: $fila.find('td:eq(11)').find('p').html(),
                            _token: "<?php echo e(csrf_token()); ?>"},
                        success: function (datos) {
                            //$fila.find('td.resultado').html('<span class="text-success">Procesado</span>');
                        },
                        error: function () {
                            //alert("ERROR");
                            console.log("Error ID: " + $fila.find('td:eq(0)').find('input').val()+', descripcion: '+$.trim($fila.find('td:eq(1)').html()));
                        },
                        complete: function () {
                            filaActual++;
                            mostrarProgreso();
                            procesarProductos();
                        }
                    });
                } else
                {
                    indiceCategoria++;
                    if (indiceCategoria < totalCategorias)
                    {
                        obtenerProductos();
                    } else
                    {
                        indiceCatalogo++;
                        if (indiceCatalogo < totalCatalogos)
                        {
                            obtenerCategorias();
                        } else
                        {
                            $tdProductosProcesados.html('<span class="text-success">Fin del proceso.</span>');
                            $tdProgresoTotal.html('Catálogos procesados: ' + indiceCatalogo + ' de ' + totalCatalogos);
                        }
                    }
                }
            }

            function mostrarProgreso() {
                $tdProductosProcesados.html('Procesando producto ' + (filaActual + 1) + ' de ' + totalFilas);
                $tdProgresoTotal.html('Catálogos procesados: ' + indiceCatalogo + ' de ' + totalCatalogos);
            }

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/acuerdo-marco/descarga/productos_adjudicados.blade.php ENDPATH**/ ?>