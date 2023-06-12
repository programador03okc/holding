
<?php $__env->startSection('estilos'); ?>
<style>
    table img {
        display: none;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cabecera'); ?>
Publicar stock de productos
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Publicar</li>
    <li class="active">Stock de productos</li>
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
                <!--<label class="col-sm-1 control-label">Stock:</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="txtStock" value="300">
                    <div class="checkbox">
                        <label>
                        <input type="checkbox" id="chkForzar"> Forzar actualización
                        </label>
                    </div>
                </div>-->
                <label class="col-sm-1 col-sm-offset-1 control-label">Empresa:</label>
                <div class="col-sm-2">
                    <select class="form-control" id="selectEmpresa" name="empresa">
                        <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($empresa->id); ?>"><?php echo e($empresa->empresa); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="chkForzar"> Forzar actualización
                        </label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button id="btnObtenerAcuerdos" class="btn btn-default">Obtener acuerdos</button>
                </div>
                <label class="col-sm-1 control-label">Acuerdo:</label>
                <div class="col-sm-2" id="tdAcuerdos">
                    <div class="form-control-static">En espera</div>
                </div>
                <div class="col-sm-1">
                    <button id="btnIniciar" disabled class="btn btn-primary">Iniciar</button>
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
        <div id="divProductos">

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src='<?php echo e(asset("mgcp/js/util.js")); ?>'></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            const TIPO_CAMBIO = parseFloat('<?php echo e($tipoCambio); ?>');
            const MONTO_PCO = 150000; //Monto de proformas compra ordinaria, sugerido por Jonathan Medina
            let totalCatalogos;
            let totalCategorias;
            let filaActual;
            let totalFilas;
            let indiceCatalogo;
            let indiceCategoria;
            //let stockPublicar;
            let $divProductos = $('#divProductos');
            let $tbodyProductos;
            let $tdCatalogos = $('#tdCatalogos');
            let $tdCatalogoActual = $('#tdCatalogoActual');
            let $tdCategorias = $('#tdCategorias');
            let $tdCategoriaActual = $('#tdCategoriaActual');
            let $tdProductosProcesados = $('#tdProductosProcesados');
            let $tdProgresoTotal = $('#tdProgresoTotal');
            let $botonObtenerAcuerdos = $('#btnObtenerAcuerdos');
            let $botonIniciar = $('#btnIniciar');
            let $tdAcuerdos = $('#tdAcuerdos');
            let $selectEmpresa = $('#selectEmpresa');
            let $selectAcuerdos;
            let $selectCatalogos;
            let $selectCategorias;


            $botonObtenerAcuerdos.click(function() {
                $tdAcuerdos.html('<div class="form-control-static">Obteniendo datos...</div>');
                $botonObtenerAcuerdos.prop('disabled', true);
                $botonIniciar.prop('disabled', true);
                $selectEmpresa.prop('disabled', true);
                //$selectTipo.prop('disabled', true);
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.peru-compras.obtener-acuerdos')); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        pagina: 'productos',
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(resultado) {
                        if (resultado.mensaje == 'ok') {
                            if (resultado.data.length > 0) {
                                let select = '<select class="form-control" id="selectAcuerdos">';
                                for (let indice in resultado.data) {
                                    select += '<option value="' + resultado.data[indice].id + '">' + resultado.data[indice].descripcion + '</option>';
                                }
                                select += '</select>';
                                $tdAcuerdos.html(select);
                                $selectAcuerdos = $('#selectAcuerdos');
                                $botonIniciar.prop('disabled', false);
                            } else {
                                $tdAcuerdos.html('<span class="text-danger">Sin acuerdos. Operación no puede continuar.</span>');
                            }
                        } else {
                            $tdAcuerdos.html('<span class="text-danger">Error al iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo.</span>');
                        }

                    },
                    error: function() {
                        $tdAcuerdos.html('<div class="form-control-static"><span class="text-danger">Error al obtener acuerdos. Por favor inténtelo de nuevo.</span></div>');
                    },
                    complete: function() {
                        $botonObtenerAcuerdos.prop('disabled', false);
                        $botonIniciar.prop('disabled', false);
                        $selectEmpresa.prop('disabled', false);
                        //$selectTipo.prop('disabled', false);
                    }
                });
            });

            $botonIniciar.click(function() {
                indiceCatalogo = 0;
                indiceCategoria = 0;
                //stockPublicar = parseInt($('#txtStock').val());
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
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        idAcuerdo: $selectAcuerdos.val(),
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(resultado) {
                        if (resultado.mensaje == 'ok') {
                            if (resultado.data.length > 0) {
                                let cadena = '<select class="form-control input-sm" id="selectCatalogos">';
                                for (let indice in resultado.data) {
                                    cadena += '<option value="' + resultado.data[indice].Value + '">' + resultado.data[indice].Text + '</option>'; //datos[indice].descripcion + ', ';
                                }
                                cadena += '</select>';
                                $tdCatalogos.html(cadena);
                                $selectCatalogos = $('#selectCatalogos');
                                totalCatalogos = $selectCatalogos.find('option').length;
                                obtenerCategorias();
                            } else {
                                //$tdCatalogos.html('<span class="text-danger">Sin catálogos. Operación no puede continuar.</span>');
                                console.log('Obtener catálogos: Sin catálogos. Reintentando...');
                                obtenerCatalogos();
                            }
                        } else {
                            console.log('Obtener catálogos: Error al iniciar sesión. Reintentando...');
                            obtenerCatalogos();
                        }
                    },
                    error: function() {
                        //$tdCatalogos.html('<div class="form-control-static"><span class="text-danger">Error al obtener catálogos. Reintentando...</span></div>');
                        console.log('Obtener catálogos: Error desconocido. Reintentando...');
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
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        idCatalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(resultado) {
                        if (resultado.mensaje == 'ok') {
                            indiceCategoria = 0;
                            if (resultado.data.length > 0) {
                                let cadena = '<select class="form-control input-sm" id="selectCategorias">';
                                for (let indice in resultado.data) {
                                    cadena += '<option value="' + resultado.data[indice].Value + '">' + resultado.data[indice].Text + '</option>'; //datos[indice].descripcion + ', ';
                                }
                                cadena += '</select>';
                                $tdCategorias.html(cadena);
                                $selectCategorias = $('#selectCategorias');
                                totalCategorias = $selectCategorias.find('option').length;
                                obtenerProductos();
                            } else {
                                //$tdCategorias.html('<span class="text-danger">Sin categorías. Operación no puede continuar.</span>');
                                console.log('Obtener categorías: Sin categorías. Reintentando...');
                                obtenerCategorias();
                            }
                        } else {
                            //$tdCatalogos.html('<span class="text-danger">Error al iniciar sesión. Verifique la contraseña almacenada en el sistema e inténtelo de nuevo.</span>');
                            console.log('Obtener categorías: Error al iniciar sesión. Reintentando...');
                            obtenerCategorias();
                        }
                    },
                    error: function() {
                        //$tdCatalogos.html('<div class="form-control-static"><span class="text-danger">Error al obtener catálogos. Reintentando...</span></div>');
                        console.log('Obtener categorías: Error desconocido. Reintentando...');
                        obtenerCategorias()
                    }
                });
            }

            function obtenerProductos() {
                filaActual = 0;
                $tdCategoriaActual.html($selectCategorias.find('option:eq(' + indiceCategoria + ')').html() + ' (' + (indiceCategoria + 1) + ' de ' + totalCategorias + ')');
                $tdProductosProcesados.html('Obteniendo datos...');
                //console.log("Obteniendo productos de catalogo "+$selectCatalogos.find('option:eq(' + indiceCatalogo + ')').html()+" categoria "+$selectCategorias.find('option:eq(' + indiceCategoria + ')').html());
                //confirm("se van a borrar los productos");
                $divProductos.html('');
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-productos.obtener-productos')); ?>",
                    type: 'post',
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        idAcuerdo: $selectAcuerdos.val(),
                        idCatalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                        idCategoria: $selectCategorias.find('option:eq(' + indiceCategoria + ')').val(),
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(datos) {
                        $divProductos.html(datos);

                        $tbodyProductos = $divProductos.find('tbody');
                        totalFilas = $tbodyProductos.find('tr').length;
                        //confirm("Total filas es "+totalFilas);
                        procesar();
                    },
                    error: function() {
                        //$divProductos.html('<div class="text-center">Error al obtener los productos. Reintentando...</div>');
                        console.log("Obtener productos: Error desconocido. Reintentando...");
                        obtenerProductos();
                    }
                });
            }

            function procesar() {
                //console.log("Fila actual es "+filaActual+", total filas es "+totalFilas);
                
                if (filaActual < totalFilas) {
                    let $fila = $tbodyProductos.find('tr:eq(' + filaActual + ')');
                    let precioUnitarioSoles = ($.trim($fila.find('td:eq(3)').html()) == 'USD' ? TIPO_CAMBIO : 1) * parseFloat($.trim($fila.find('td:eq(4)').html()));
                    let stockCalculado=Math.round(MONTO_PCO / precioUnitarioSoles)*2;
                    let stockPublicar = stockCalculado > 300 ? 300 : stockCalculado;
                    let stockVigente = parseInt($.trim($fila.find('td:eq(6)').html()));
                    
                    let stockPublicado = parseInt($.trim($fila.find('td:eq(7)').html()));

                    //BORRAR
                    //stockPublicar=1;
                    //REACTIVAR
                    if ($('#chkForzar').is(':checked') == false) {
                        if (stockPublicar == 0 && stockVigente == 0 || stockPublicar == 0 && stockPublicado == 0) {
                            filaActual++;
                            mostrarProgreso();
                            procesar();
                            return;
                        }
                        if (stockPublicar <= stockVigente && stockPublicar != 0 || stockPublicar <= stockPublicado && stockPublicar != 0) {
                            filaActual++;
                            mostrarProgreso();
                            procesar();
                            return;
                        }
                    }
                    $.ajax({
                        url: "<?php echo e(route('mgcp.acuerdo-marco.publicar.stock-productos.procesar')); ?>",
                        type: 'post',
                        data: {
                            idEmpresa: $selectEmpresa.val(),
                            acuerdo: $selectAcuerdos.find('option:selected').val(),
                            catalogo: $selectCatalogos.find('option:eq(' + indiceCatalogo + ')').val(),
                            categoria: $selectCategorias.find('option:eq(' + indiceCategoria + ')').val(),
                            stockAnterior: stockVigente,
                            stockPublicar: stockPublicar,
                            descripcion: $fila.find('td:eq(1)').html(),
                            idPc: $fila.find('td:eq(9)').find('a:eq(0)').attr('onclick').match(/\d+/)[0],
                            _token: "<?php echo e(csrf_token()); ?>"
                        },
                        success: function(datos) {
                            //console.log(datos.mensaje);
                            //$fila.find('td.resultado').html('<span class="text-success">Procesado</span>');
                        },
                        error: function() {
                            console.log("Error al procesar " + $fila.find('td:eq(8)').find('a:eq(0)').attr('onclick').match(/\d+/)[0]);
                            //$fila.find('td.resultado').html('<span class="text-danger">Error</span>');
                        },
                        complete: function() {
                            mostrarProgreso();
                            filaActual++;
                            procesar();
                        }
                    });
                } else {
                    //console.log("Fin de filas, obtener productos de otra categoria");
                    indiceCategoria++;
                    if (indiceCategoria < totalCategorias) {
                        //alert("Clic en obtenerProductos");
                        obtenerProductos();
                    } else {
                        //console.log("fin categorias, obtener categorias de otro catalogo");
                        indiceCatalogo++;
                        if (indiceCatalogo < totalCatalogos) {
                            obtenerCategorias();
                        } else {
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
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/acuerdo-marco/publicar/stock-productos.blade.php ENDPATH**/ ?>