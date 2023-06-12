

<?php $__env->startSection('cabecera'); ?>
Aprobadores de cuadros de presupuesto
<?php $__env->stopSection(); ?>

<?php $__env->startSection('estilos'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="#">Inicio</a></li>
    <li class="active">Cuadros de presupuesto</li>
    <li class="active">Ajustes</li>
    <li class="active">Aprobadores</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">Por monto</h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <table id="tablePorMonto" class="table table-condensed table-hover table-striped table-bordered" style="font-size: small; width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">Usuario</th>
                            <th class="text-center" style="width: 15%">Valor venta máximo</th>
                            <th class="text-center" style="width: 15%">Margen mínimo</th>
                            <th class="text-center" style="width: 10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">Fuera de monto</h3>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <table id="tableFueraMonto" class="table table-condensed table-bordered" style="font-size: small; width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">Usuario</th>
                            <th class="text-center" style="width: 10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="tdFueraMontoUsuario"><?php echo e($aprobadorFueraMonto->usuario->name); ?></td>
                            <td class="text-center">
                                <button id="btnEditarFueraMonto" title="Editar" data-id="<?php echo e($aprobadorFueraMonto->usuario->id); ?>" class="btn btn-primary editar btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarFueraMonto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar aprobador</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Aprobador</label>
                    <select id="selectAprobadoresFueraMonto" class="form-control">
                        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($usuario->id); ?>"><?php echo e($usuario->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group" id="divMensajeFueraMonto">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnActualizarAprobadorFueraMonto">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGestionarAprobadorMonto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="spanOperacionAprobadorMonto"></span> aprobador</h4>
            </div>
            <div class="modal-body">
                <form id="formPorMonto">
                    <?php echo e(csrf_field()); ?>

                    <input type="hidden" name="id" />
                    <div class="form-group">
                        <label>Aprobador</label>
                        <select class="form-control" name="usuario">
                            <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($usuario->id); ?>"><?php echo e($usuario->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Valor de venta máximo</label>
                        <div class="input-group">
                            <span class="input-group-addon">S/</span>
                            <input type="text" class="form-control" name="valor_venta" placeholder="Valor de venta">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Margen mínimo</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="margen_minimo" placeholder="Margen mínimo">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="form-group" id="divMensajePorMonto">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPorMonto">Guardar</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<link href="<?php echo e(asset("assets/datatables/css/dataTables.bootstrap.min.css")); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo e(asset("assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css")); ?>" rel="stylesheet" type="text/css"/>

<script src="<?php echo e(asset("assets/datatables/js/jquery.dataTables.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/datatables/js/dataTables.bootstrap.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js")); ?>"></script>
<script src="<?php echo e(asset("assets/loadingoverlay/loadingoverlay.min.js")); ?>"></script>

<script src="<?php echo e(asset("mgcp/js/util.js?v=2")); ?>"></script>
<script>

$(document).ready(function () {
    Util.seleccionarMenu(window.location);

    const $tablePorMonto = $('#tablePorMonto').DataTable({
        pageLength: 20,
        dom: 'Bfrtip',
        serverSide: true,
        initComplete: function (settings, json) {
            var $filter = $('#tablePorMonto_filter');
            var $input = $filter.find('input');
            $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>');
            $input.unbind();
            $input.bind('keyup', function (e) {
                if (e.keyCode == 13) {
                    $('#btnBuscar').trigger('click');
                }
            });
            $('#btnBuscar').click(function () {
                $tablePorMonto.search($input.val()).draw();
            });
        },
        drawCallback: function (settings) {
            $('#tablePorMonto_filter input').attr('disabled', false);
            $('#btnBuscar').html('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>').attr('disabled', false);
            $('#tablePorMonto_filter input').focus();
        },
        language: {
            url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        order: [[0, "desc"]],
        ajax: {
            url: "<?php echo e(route('mgcp.cuadro-costos.ajustes.aprobadores.data-lista')); ?>",
            type: "POST",
            data: {_token: "<?php echo e(csrf_token()); ?>"},
            complete: function () {
                //ActualizarDatos.contarFiltros();
            }
        },
        columns: [
            {data: 'name', name: 'users.name'},
            {data: 'valor_venta', searchable: false},
            {data: 'margen_minimo', searchable: false}
        ],
        columnDefs: [
            {orderable: false, targets: [3]},
            {className: "text-center", targets: [2, 3]},
            {className: "text-right", targets: [1]},
            {render: function (data, type, row) {
                    return 'S/' + Util.formatoNumero(row.valor_venta, 2, '.', ',');
                }, targets: 1
            },
            {render: function (data, type, row) {
                    return row.margen_minimo + '%';
                }, targets: 2
            },
            {render: function (data, type, row) {
                    var botones = '<button title="Editar" data-id="' + row.id + '" class="btn btn-primary editar btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>';
                    botones += ' <button title="Eliminar" data-id="' + row.id + '" class="btn btn-danger eliminar btn-xs"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';
                    return botones;
                }, targets: 3
            },
        ],
        buttons: [
            {
                text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo',
                action: function () {
                    $('#spanOperacionAprobadorMonto').html('Nuevo');
                    $('#btnGuardarPorMonto').html('Registrar');
                    $('#modalGestionarAprobadorMonto').modal('show');
                },
                className: 'btn-sm'
            }
        ]
    });

    $tablePorMonto.on('processing.dt', function (e, settings, processing) {
            if (processing) {
                $(e.currentTarget).LoadingOverlay("show", {
                    imageAutoResize: true,
                    progress: true,
                    imageColor: "#3c8dbc"
                });
            } else {
                $(e.currentTarget).LoadingOverlay("hide", true);
            }
        });

    $('#tablePorMonto').on('click', 'button.eliminar', function () {
        if (confirm("¿Está seguro de eliminar el registro?"))
        {
            var $boton = $(this);
            $.ajax({
                url: '<?php echo e(route("mgcp.cuadro-costos.ajustes.aprobadores.eliminar-aprobador-por-monto")); ?>',
                data: {id: $boton.data('id'), _token: '<?php echo e(csrf_token()); ?>'},
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.tipo == 'success')
                    {
                        $boton.closest('tr').fadeOut(300, function () {
                            $tablePorMonto.ajax.reload();
                        });
                    } else
                    {
                        alert(data.mensaje);
                    }

                },
                error: function (xhr, status) {
                    alert('Hubo un problema al eliminar al usuario. Por favor actualice la página e intente de nuevo.');
                },
                complete: function ()
                {
                    $boton.prop('disabled', false);
                }
            });
        }
    });

    $('#btnEditarFueraMonto').click(function () {
        $('#selectAprobadoresFueraMonto').val($(this).data('id'));
        $('#modalEditarFueraMonto').modal('show');
    });

    $('#btnActualizarAprobadorFueraMonto').click(function () {
        var $boton = $(this);
        $boton.prop('disabled', true);
        $.ajax({
            url: '<?php echo e(route("mgcp.cuadro-costos.ajustes.aprobadores.actualizar-aprobador-fuera-monto")); ?>',
            data: {usuario: $('#selectAprobadoresFueraMonto').val(), _token: '<?php echo e(csrf_token()); ?>'},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                alert(data.mensaje);
                if (data.tipo=='success')
                {
                    $('#tdFueraMontoUsuario').html($('#selectAprobadoresFueraMonto').find('option:selected').html());
                    $('#modalEditarFueraMonto').modal('hide');
                }
                
            },
            error: function (xhr, status) {
                alert('Hubo un problema al cambiar al usuario. Por favor actualice la página e intente de nuevo.');
            },
            complete: function ()
            {
                $boton.prop('disabled', false);
            }
        });
    });

    $('#tablePorMonto').on('click', 'button.editar', function () {
        $('#spanOperacionAprobadorMonto').html('Editar');
        $('#btnGuardarPorMonto').html('Actualizar');
        var $boton = $(this);
        var $modal=$('#modalGestionarAprobadorMonto');
        $modal.modal('show');
        $modal.find('select, input').prop('disabled',true);
        $.ajax({
            url: '<?php echo e(route("mgcp.cuadro-costos.ajustes.aprobadores.detalles-aprobador-por-monto")); ?>',
            data: {id: $boton.data('id'), _token: '<?php echo e(csrf_token()); ?>'},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
               $modal.find('input[name=id]').val($boton.data('id'));
               $modal.find('select').val(data.id_usuario);
               $modal.find('input[name=valor_venta]').val(data.valor_venta);
               $modal.find('input[name=margen_minimo]').val(data.margen_minimo);
            },
            error: function (xhr, status) {
                alert('Hubo un problema al obtener los datos del usuario. Por favor actualice la página e intente de nuevo.');
            },
            complete: function ()
            {
                $modal.find('select, input').prop('disabled',false);
                $boton.prop('disabled', false);
            }
        });
    });

    $('#btnGuardarPorMonto').click(function () {
        var $boton = $(this);
        $boton.prop('disabled', true);
        var rutaRegistrar = '<?php echo e(route("mgcp.cuadro-costos.ajustes.aprobadores.registrar-aprobador-por-monto")); ?>';
        var rutaActualizar = '<?php echo e(route("mgcp.cuadro-costos.ajustes.aprobadores.actualizar-aprobador-por-monto")); ?>';
        $.ajax({
            url: ($('#spanOperacionAprobadorMonto').html() == 'Nuevo' ? rutaRegistrar : rutaActualizar),
            data: $('#formPorMonto').serialize(),
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                alert(data.mensaje);
                if (data.tipo == 'success')
                {
                    $tablePorMonto.ajax.reload();
                     $('#modalGestionarAprobadorMonto').modal('hide');
                }
                //Util.mensaje('#divMensajePorMonto', data.tipo, data.mensaje);
            },
            error: function (xhr, status) {
                alert('Hubo un problema al guardar el usuario. Por favor actualice la página e intente de nuevo.');
            },
            complete: function ()
            {
                $boton.prop('disabled', false);
            }
        });
    });
    
    $('#modalEditarFueraMonto').on('show.bs.modal', function (e) {
        $('#divMensajeFueraMonto').html('');
    });
    
    $('#modalGestionarAprobadorMonto').on('show.bs.modal', function (e) {
        $('#divMensajePorMonto').html('');
    });

});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/cuadro-costo/ajuste/aprobadores.blade.php ENDPATH**/ ?>