
<?php $__env->startSection('estilos'); ?>
    <link href="<?php echo e(asset('assets/bootstrap-select/css/bootstrap-select.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        table img {
            display: none;
        }
        .box-body h5{
            font-weight: 600;
        }
        .box-body h5 span{
            font-weight: normal;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        body .bootstrap-select .dropdown-menu {
            min-width: 100% !important;
            max-width: 100% !important;
            font-size: 12.5px;
            overflow: visible;
            overflow-x: scroll !important;
        }
        .input-group .form-producto {
            height: 35px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cabecera'); ?>
    Publicar plazos de entrega
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <ol class="breadcrumb">
        <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
        <li class="active">Acuerdo marco</li>
        <li class="active">Publicar</li>
        <li class="active">Plazos de entrega</li>
    </ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Configuración</h3>
        </div>
        <div class="box-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <h5>Empresa</h5>
                    <div class="input-group">
                        <select class="form-control" id="selectEmpresa" name="empresa">
                            <?php $__currentLoopData = $empresas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empresa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($empresa->id); ?>" data-pc="<?php echo e($empresa->id_pc); ?>"><?php echo e($empresa->empresa); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-flat" onclick="obtenerAcuerdos();">Obtener Acuerdos</button>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Acuerdos <span class="pull-right text-success" id="MessageAcuerdo">En espera...</span></h5>
                    <select class="form-control" id="selectAcuerdo" disabled></select>
                </div>
                <div class="col-md-3">
                    <h5>Categorías <span class="pull-right text-success" id="MessageCategoria"></span></h5>
                    <div class="base-select" id="BaseCategoria">
                        <select class="form-control" id="selectCategoriaView" disabled><option value="">No hay selección</option></select>
                        <div id="divCategorias"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <h5>Regiones</h5>
                    <select id="selectRegion" name="region" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple>
                        <?php $__currentLoopData = $regiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($region->id_portal); ?>"><?php echo e($region->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <h5>Productos <span class="pull-right text-success" id="MessageProducto"></span></h5>
                    <div class="input-group">
                        <input type="text" class="form-control form-producto" name="producto_nombre" id="producto_nombre" readonly>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-flat" onclick="modalProductos();"> Lista de Productos</button>
                        </span>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Plazo</h5>
                            <input type="number" class="form-control text-center" id="txtPlazo" value="60">
                        </div>
                        <div class="col-md-4">
                            <h5>Días Vigencia</h5>
                            <input type="number" class="form-control text-center" id="txtLimite" value="7">
                        </div>
                        <div class="col-md-4 text-center">
                            <button class="btn btn-primary btn-block btn-flat" id="btnIniciar" style="margin-top: 34px;">Iniciar</button>
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
            <table class="table table-condensed" style="width: 100%">
                <thead>
                    <tr>
                        <th style="width: 25%" class="text-center">Categoría actual</th>
                        <th style="width: 25%" class="text-center">Región actual</th>
                        <th style="width: 25%" class="text-center">Provincia actual</th>
                        <th style="width: 25%" class="text-center">Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="tdCategoriaActual" class="text-center"></td>
                        <td id="tdRegionActual" class="text-center"></td>
                        <td id="tdProvinciaActual" class="text-center"></td>
                        <td id="tdProgreso"></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <div class="modal fade" id="modalProductos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <table class="table" id="tableProductos">
                        <thead>
                            <tr>
                                <th width="15%" class="text-center">-</th>
                                <th width="15%" class="text-center">Descripción</th>
                                <th width="15%" class="text-center">Marca</th>
                                <th width="15%" class="text-center">Modelo</th>
                                <th width="25%" class="text-center">Nro parte</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/bootstrap-select/js/bootstrap-select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/util.js')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/timer.js')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/peru-compras.js')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/producto/catalogo.js')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/acuerdo-marco/producto/categoria.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            $(".sidebar-mini").addClass("sidebar-collapse");

            let indiceCategoria;
            let indiceProvincia;
            let indiceRegion;

            let listaCategorias;
            let listaRegiones;
            let listaProvincias;
            let idAcuerdo;
            let plazo;

            const $selectEmpresa = $('#selectEmpresa');
            const $selectAcuerdos = $('#selectAcuerdo');
            const $selectCategorias = $('#selectCategoria');
            const $selectCategoriaView = $('#selectCategoriaView');
            const $selectProductoView = $('#selectProductoView');

            const $spanAcuerdos = $('#MessageAcuerdo');
            const $spanCategorias = $('#MessageCategoria');
            const $spanProductos = $('#MessageProducto');

            const $tdCategoriaActual = $('#tdCategoriaActual');
            const $tdRegionActual = $('#tdRegionActual');
            const $tdProvinciaActual = $('#tdProvinciaActual');
            const $tdProgreso = $('#tdProgreso');
            const $btnIniciar = $('#btnIniciar');

            const catalogo = new Catalogo("<?php echo e(csrf_token()); ?>");
            const categoria = new Categoria("<?php echo e(csrf_token()); ?>");
            const peruCompras = new PeruCompras("<?php echo e(csrf_token()); ?>");
            let timer;

            obtenerAcuerdos = () => {
                var route = "<?php echo e(route('mgcp.acuerdo-marco.peru-compras.obtener-acuerdos')); ?>";
                $('#selectCategoria').selectpicker('destroy');
                $('#selectCategoria').val('default').selectpicker("refresh");
                $spanCategorias.text('En espera...');
                $selectEmpresa.prop('disabled', true);
                $spanAcuerdos.text('Obteniendo acuerdos...');
                $btnIniciar.prop('disabled', true);

                $.when(peruCompras.obtenerAcuerdos($selectEmpresa.val(), 'mejorar_plazo', route)).then(function (respuesta) {
                    let acuerdos = '';
                    for (let indice in respuesta.data) {
                        acuerdos += `<option value="${respuesta.data[indice].id}">${respuesta.data[indice].descripcion}</option>`;
                    }
                    
                    $btnIniciar.prop('disabled', false);
                    $selectEmpresa.prop('disabled', false);
                    $selectAcuerdos.html(acuerdos).trigger('change');
                    $selectAcuerdos.removeAttr('disabled');
                    $spanAcuerdos.text('');
                    obtenerCategoriasPorAcuerdo($selectAcuerdos.find('option:selected').text());
                }, function() {
                    obtenerAcuerdos();
                });
            }

            obtenerCategoriasPorAcuerdo = (descripcionAm,) => {
                const $divCategorias = $('#divCategorias');
                $divCategorias.find('select').val('default').selectpicker("refresh");
                $spanCategorias.text('Obteniendo categorías...');

                $.ajax({
                    url: route('mgcp.acuerdo-marco.publicar.plazos-entrega.obtener-categorias-por-acuerdo'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        descripcionAm: descripcionAm,
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(data) {
                        let filas = `<select name="categoria" id="selectCategoria" class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple>`;
                        for (let indice in data) {
                            filas+=`<option value="${data[indice].id}">${data[indice].categoria}</option>`;
                        }
                        filas += '</select>';

                        $selectCategoriaView.addClass('d-none')
                        $divCategorias.html(filas);
                        $divCategorias.find('select').selectpicker('render');
                        $spanCategorias.text('');
                    }
                });
            }

            $selectEmpresa.change(() => {
                obtenerAcuerdos();
            });

            $selectAcuerdos.on('change', function() {
                obtenerCategoriasPorAcuerdo($(this).find('option:selected').text(), 1);
            });

            obtenerProvincias = () => {
                var route = "<?php echo e(route('mgcp.acuerdo-marco.peru-compras.obtener-provincias')); ?>";
                if (indiceRegion == listaRegiones.length) {
                    indiceCategoria++;
                    indiceRegion = 0;
                    if (indiceCategoria == listaCategorias.length) {
                        $tdProgreso.html('<span class="text-success">Fin del proceso</span>');
                        timer.stop();
                    } else {
                        obtenerProvincias();
                    }
                } else {
                    indiceProvincia = 0;
                    listaProvincias = [];
                    $tdRegionActual.html(
                        `${listaRegiones[indiceRegion].nombre} (${indiceRegion+1} de ${listaRegiones.length})`
                    );
                    $tdCategoriaActual.html(
                        `${listaCategorias[indiceCategoria].descripcion} (${indiceCategoria+1} de ${listaCategorias.length})`
                    );
                    $tdProvinciaActual.html('Obteniendo provincias...');
                    $.when(peruCompras.obtenerProvincias($selectEmpresa.val(), listaRegiones[indiceRegion].id, route)).then(function (respuesta) {
                        if (respuesta.tipo == 'danger') {
                            $tdProvinciaActual.html(
                                `<span class="text-danger">${respuesta.mensaje}</span>`);
                        } else {
                            for (let indice in respuesta.data) {
                                listaProvincias.push({id: respuesta.data[indice].id, nombre: respuesta.data[indice].nombre});
                            }
                            $tdProgreso.html('Procesando...');
                            procesar();
                        }
                    }, function() {
                        obtenerProvincias();
                    });
                }
            }

            obtenerRegiones = () => {
                indiceRegion = 0;
                listaRegiones = [];
                $('#selectRegion option:selected').each(function() {
                    listaRegiones.push({
                        id: $(this).val(),
                        nombre: $(this).text()
                    });
                });
                $tdRegionActual.html(`${ listaRegiones[indiceRegion].nombre } (${ indiceRegion + 1 } de ${ listaRegiones.length })`);
            };

            obtenerCategorias = () => {
                indiceCategoria = 0;
                listaCategorias = [];
                $('#selectCategoria option:selected').each(function() {
                    listaCategorias.push({
                        id: $(this).val(),
                        descripcion: $(this).text()
                    });
                });
                $tdCategoriaActual.html(`${ listaCategorias[indiceCategoria].descripcion } (${ indiceCategoria + 1 } de ${ listaCategorias.length })`);
            };

            modalProductos = () => {
                var newListaCategorias = [];
                $('#selectCategoria option:selected').each(function() {
                    newListaCategorias.push($(this).val());
                });
                if (newListaCategorias.length > 0) {
                    $('#modalProductos').modal('show');
                }
            }

            obtenerProductos = () => {
                const $divProductos = $('#divProductos');
                $divProductos.find('select').val('default').selectpicker("refresh");
                $spanProductos.text('Obteniendo productos...');

                var newListaCategorias = [];
                $('#selectCategoria option:selected').each(function() {
                    newListaCategorias.push($(this).val());
                });

                $.ajax({
                    url: route('mgcp.acuerdo-marco.publicar.plazos-entrega.obtener-productos-por-categoria'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        categoria: newListaCategorias,
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(data) {
                        let filas = ``;
                        for (let indice in data) {
                            filas+=`<option value="${ data[indice].id }">${ data[indice].descripcion }</option>`;
                        }
                        //$selectProductoView.addClass('d-none');
                        //$divProductos.html(filas);
                        //$divProductos.find('select').selectpicker('render');
                        $spanProductos.text('');
                    }
                });
            }

            procesar = () => {
                timer.reset(80000);
                $tdProvinciaActual.html(`${ listaProvincias[indiceProvincia].nombre } (${ indiceProvincia + 1 } de ${ listaProvincias.length })`);
                //let $descripcion = ($selectTipo.val() == 1) ? $('#selectProducto').val() : $('#producto_nombre').val();
                $.ajax({
                    url: "<?php echo e(route('mgcp.acuerdo-marco.publicar.plazos-entrega.procesar')); ?>",
                    //url: route('test-plazos'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        idEmpresa: $selectEmpresa.val(),
                        idAcuerdo: idAcuerdo,
                        idCategoria: listaCategorias[indiceCategoria].id,
                        idProvincia: listaProvincias[indiceProvincia].id,
                        //tipoProducto: $selectTipo.val(),
                        //descripcion: $descripcion,
                        plazo: plazo,
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.tipo == "success") {
                            indiceProvincia++;
                            if (indiceProvincia == listaProvincias.length) {
                                indiceRegion++;
                                obtenerProvincias();
                            } else {
                                procesar();
                            }
                        }
                    },
                    error: function() {
                        console.log("ERROR en procesar");
                        procesar();
                    }
                });
            };

            $('#btnIniciar').click(function() {
                timer = new Timer(function() {
                    console.log("Función disparada el " + new Date().toLocaleTimeString());
                    procesar();
                }, 40000);
                //timer.start();
                //console.log("Llamada a iniciar");
                if ($('#selectRegion option:selected').length == 0) {
                    alert("Seleccione al menos una región para continuar");
                    return;
                }
                idAcuerdo = $('#selectAcuerdo').val();
                idAcuerdo = idAcuerdo.substring(idAcuerdo.indexOf("-") + 1);
                idAcuerdo = idAcuerdo.substring(0, idAcuerdo.indexOf("-"));
                plazo = $('#txtPlazo').val();
                $selectEmpresa.prop('disabled', true);
                $('#selectAcuerdo').prop('disabled', true);
                $('#selectRegion').prop('disabled', true);
                $(this).prop('disabled', true);
                $tdProgreso.html('Por favor espere...');
                //obtenerRegiones no es peticion AJAX
                obtenerRegiones();
                obtenerCategorias();
                obtenerProvincias();
                //obtenerCatalogos();
            });
         });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp\resources\views/mgcp/acuerdo-marco/publicar/plazos-entrega.blade.php ENDPATH**/ ?>