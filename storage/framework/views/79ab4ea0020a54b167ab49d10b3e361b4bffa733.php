

<?php $__env->startSection('estilos'); ?>
    <link href="<?php echo e(asset('assets/datatables/css/dataTables.bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cabecera'); ?> Lista de usuarios <?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<ol class="breadcrumb">
    <li><a href="<?php echo e(route('mgcp.home')); ?>">Inicio</a></li>
    <li class="active">Usuarios</li>
    <li class="active">Lista</li>
</ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>
<div class="box box-solid">
    <div class="box-body">
        <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="col-sm-8 col-sm-offset-2">
            <div class="table-responsive">
                <table style="width: 100%" id="tableUsuarios" class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Activo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e(($user->activo ? "Sí" : "No")); ?></td>
                            <td class="text-center">
                                <a href="<?php echo e(route('mgcp.usuarios.editar',$user->id)); ?>" class="btn btn-primary btn-sm">
                                    Editar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
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

    <script src='<?php echo e(asset("mgcp/js/util.js")); ?>'></script>
    <script>
        $(document).ready(function() {

            Util.seleccionarMenu(window.location);
            $('#tableUsuarios').DataTable({
                dom: 'Bfrtip'
                , pageLength: 20
                , language: {
                    url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }
                , columnDefs: [{
                        orderable: false
                        , targets: [3]
                    }
                    , {
                        className: "text-left"
                        , targets: [0, 1]
                    }
                    , {
                        className: "text-center"
                        , targets: [2, 3]
                    }
                ]
                , buttons: [{
                    text: '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo'
                    , action: function() {
                        window.location = "<?php echo e(route('mgcp.usuarios.nuevo')); ?>";
                    }
                }]
            });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/usuario/lista.blade.php ENDPATH**/ ?>