

<?php $__env->startSection('cabecera'); ?> Licencias para cuadros de presupuesto <?php $__env->stopSection(); ?>

<?php $__env->startSection('estilos'); ?>
    <link href="<?php echo e(asset('assets/datatables/css/dataTables.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('assets/lobibox/dist/css/lobibox.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <ol class="breadcrumb">
        <li><a href="#">Inicio</a></li>
        <li class="active">Cuadros de presupuesto</li>
        <li class="active">Ajustes</li>
        <li class="active">Licencias</li>
    </ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>
<div class="box box-solid">
    <div class="box-header with-border"><h3 class="box-title">Lista de licencias activas</h3></div>

    <div class="box-body">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <table id="tableLicencia" class="table table-condensed table-hover table-striped table-bordered" style="font-size: small; width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 20%">Marca</th>
                            <th class="text-center" style="width: 25%">Nro Parte</th>
                            <th class="text-center">Descripción</th>
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLicencia" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar licencia</h4>
            </div>
            <div class="modal-body">
                <form id="formulario" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <h5>Marca</h5>
                        <input type="text" name="marca" class="form-control" placeholder="Ingrese la marca">
                    </div>
                    <div class="form-group">
                        <h5>Nro de parte</h5>
                        <input type="text" name="part_no" class="form-control" placeholder="Ingrese el part number">
                    </div>
                    <div class="form-group">
                        <h5>Descripción</h5>
                        <textarea name="descripcion" class="form-control" rows="4" placeholder="Ingrese la descripción" style="resize: none;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardar();">Guardar</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/datatables/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/datatables/js/dataTables.bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/lobibox/dist/js/lobibox.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/loadingoverlay/loadingoverlay.min.js')); ?>"></script>
    <script src="<?php echo e(asset('mgcp/js/util.js?v=2')); ?>"></script>
    <script>
        $(document).ready(function () {
            Util.seleccionarMenu(window.location);
            listar();
        });

        function listar() {
            var $tabla = $('#tableLicencia').DataTable({
                dom: 'Bfrtip',
                pageLength: 20,
                language: {
                    url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                serverSide: true,
                initComplete: function (settings, json) {
                    const $filter = $('#tableLicencia_filter');
                    const $input = $filter.find('input');
                    $filter.append('<button id="btnBuscar" class="btn btn-default btn-sm pull-right" type="button"><i class="fa fa-search"></i></button>');
                    $input.off();
                    $input.on('keyup', (e) => {
                        if (e.key == 'Enter') {
                            $('#btnBuscar').trigger('click');
                        }
                    });
                    $('#btnBuscar').on('click', (e) => {
                        $tabla.search($input.val()).draw();
                    });
                },
                drawCallback: function (settings) {
                    $('#tableLicencia_filter input').prop('disabled', false);
                    $('#btnBuscar').html('<i class="fa fa-search"></i>').prop('disabled', false);
                    $('#tableLicencia_filter input').trigger('focus');
                },
                order: [[2, 'asc']],
                ajax: {
                    url: route('mgcp.cuadro-costos.ajustes.licencias.listar'),
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"}
                },
                columns: [
                    {data: 'marca'},
                    {data: 'part_no'},
                    {data: 'descripcion'},
                    //{data: 'accion', orderable: false, searchable: false, className: 'text-center'}
                ],
                buttons: [
                    {
                        text: '<i class="fa fa-plus"></i> Nueva licencia',
                        action: function () { $('#modalLicencia').modal('show'); },
                        className: 'btn-sm',
                    },
                ]
            });
            $tabla.on('search.dt', function() {
                $('#tableLicencia_filter input').attr('disabled', true);
                $('#btnBuscar').html('<i class="fas fa-clock" aria-hidden="true"></i>').prop('disabled', true);
            });
            $tabla.on('init.dt', function(e, settings, processing) {
                $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
            });
            $tabla.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $(e.currentTarget).LoadingOverlay('show', { imageAutoResize: true, progress: true, imageColor: '#3c8dbc' });
                } else {
                    $(e.currentTarget).LoadingOverlay("hide", true);
                }
            });
        }

        function guardar() {
            var data = $('#formulario').serializeArray();

            console.log(data);
            $.ajax({
                type: 'POST',
                url : route('mgcp.cuadro-costos.ajustes.licencias.guardar'),
                data: data,
                dataType: 'JSON',
                success: function (response) {
                    if (response.response == 'ok') {
                        Util.notify(response.alert, response.message);
                        $('#tableLicencia').DataTable().ajax.reload(null, false);
                        $('#modalLicencia').modal('hide');
                    } else {
                        Util.notify(response.alert, response.message);
                    }
                }
            }).fail( function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
            return false;
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/cuadro-costo/ajuste/licencia.blade.php ENDPATH**/ ?>