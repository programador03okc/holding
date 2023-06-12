<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('cabecera'); ?> - Módulo de Gestión Comercial</title>
    <link rel="shortcut icon" href="<?php echo e(asset('mgcp/img/mgc.ico')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/bootstrap/dist/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/font-awesome/css/font-awesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin-lte/dist/css/AdminLTE.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin-lte/dist/css/skins/skin-blue.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/sweetalert/sweetalert2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('mgcp/css/app.css?v=14')); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <?php echo $__env->yieldContent('estilos'); ?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php echo $__env->make("mgcp/layouts/header", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make("mgcp/layouts/aside", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <div class="content-wrapper">
            <section class="content-header">
                <h1><?php echo $__env->yieldContent('cabecera'); ?></h1>
                <?php echo $__env->yieldContent('breadcrumb'); ?>
            </section>
            <section class="content">
                <?php echo $__env->yieldContent('cuerpo'); ?>
            </section>
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs"></div>Copyright &copy; 2022 Módulo de Gestión Comercial
        </footer>

        <div class="control-sidebar-bg"></div>
    </div>
    
    <script src='<?php echo e(asset("assets/jquery/dist/jquery.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/bootstrap/dist/js/bootstrap.min.js")); ?>'></script>
    <script src='<?php echo e(asset("assets/admin-lte/dist/js/adminlte.min.js")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/notificacion-model.js")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/notificacion-view.js")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/oc-propia-indicador-view.js?v=3")); ?>'></script>
    <script src='<?php echo e(asset("mgcp/js/orden-compra/propia/oc-propia-indicador-model.js?v=3")); ?>'></script>
    
    <script src='<?php echo e(asset("assets/sweetalert/sweetalert2.min.js")); ?>'></script>
    <?php echo app('Tightenco\Ziggy\BladeRouteGenerator')->generate(); ?>
    <script>
        $(document).ready(function() {
            const token = '<?php echo e(csrf_token()); ?>';
            const notificacionView = new NotificacionView(new NotificacionModel(token));
            //const paqueteView= new PaqueteView(new PaqueteModel(token));
            //paqueteView.obtenerCantidadPendientes();

            notificacionView.obtenerNoLeidas();
            //setInterval(notificacionView.obtenerNoLeidas, 300000);

            if ("<?php echo e(Auth::user()->tieneRol(125)); ?>" == "1") {
                const indicadorView = new OcPropiaIndicadorView(new OcPropiaIndicadorModel(token));
                indicadorView.obtenerIndicadorDiario();
                indicadorView.obtenerIndicadorMensual();
                //setInterval(indicadorView.obtenerIndicadorDiario, 600000);
                //setInterval(indicadorView.obtenerIndicadorMensual, 600000);
            }
        });
    </script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH C:\xampp\htdocs\mgcp_empresa\resources\views/mgcp/layouts/app.blade.php ENDPATH**/ ?>