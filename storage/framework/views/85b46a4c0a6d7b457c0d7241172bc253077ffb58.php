<?php $__env->startSection('cabecera'); ?> Bienvenido <?php $__env->stopSection(); ?>

<?php $__env->startSection('estilos'); ?>
    <link href="<?php echo e(asset('assets/lobibox/dist/css/lobibox.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('cuerpo'); ?>
<div class="box box-solid">
    <div class="box-body">
        <span id="spanMensajeBienvenida">Por favor utilice las opciones del lado izquierdo</span>
    </div>
</div>

<div class="modal fade" id="modal-password" tabindex="-1" role="dialog" aria-labelledby="modal-password" data-backdrop="static">
    <div class="modal-dialog modal-xxs" role="document">
        <div class="modal-content">
            <form id="form-renovacion-clave" method="POST" autocomplete="off" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h3 class="modal-title">Renovar clave</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Nueva clave</h5>
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="Ingrese la nueva clave" required>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 25px">
                        <div class="col-md-12">
                            <ul class="text-danger small" style="margin: 0; padding: 0; margin-left: 20px;">
                                <li>Debe contener al menos una letra minúscula</li>
                                <li>Debe contener al menos una letra mayúscula</li>
                                <li>Debe contener al menos un número</li>
                                <li>Debe contener al menos un símbolo especial</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-block btn-success shadow-none">Grabar nueva clave</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/lobibox/dist/js/lobibox.min.js')); ?>"></script>
    <script src='<?php echo e(asset("mgcp/js/util.js?v=27")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/moment.min.js?v=1")); ?>'></script>
    <script>
        var renovacion = <?php echo e($renovacion); ?>;
        $(function () {
            if (renovacion == 1) {
                $("#modal-password").modal("show");
            }

            $("#form-renovacion-clave").on("submit", function() {
                var data = $(this).serializeArray();
                $.ajax({
                    type: "POST",
                    url : route('mgcp.usuarios.renovar-clave'),
                    data: data,
                    dataType: "JSON",
                    success: function (response) {
                        Util.notify(response.alert, response.message);
                        if (response.response == 'ok') {
                            var routeLink = route('mgcp.usuarios.logout');
                            setTimeout(function(){ window.location.href = routeLink }, 2000);
                        }
                    }
                }).fail( function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                });
                return false;
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('mgcp.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\holding\resources\views/mgcp/home.blade.php ENDPATH**/ ?>