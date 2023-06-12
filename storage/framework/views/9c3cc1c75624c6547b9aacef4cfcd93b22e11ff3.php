<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Iniciar sesión - MGC</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="<?php echo e(asset('mgcp/img/mgc.ico')); ?>" />
        <link rel="stylesheet" href="<?php echo e(asset('assets/bootstrap/dist/css/bootstrap.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('assets/font-awesome/css/font-awesome.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('assets/admin-lte/dist/css/AdminLTE.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('assets/iCheck/square/blue.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('mgcp/css/app.css')); ?>">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <?php echo $__env->make('mgcp.partials.flashmsg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="login-box-body">
                <div class="login-box-logo">
                    <img src="<?php echo e(asset('mgcp/img/mgc_logo.png')); ?>" alt="">
                </div>
                <p class="login-box-msg">¡Bienvenido al Módulo de Gestión Comercial, ingrese sus credenciales!</p>

                <form method="POST" action="<?php echo e(route('login')); ?>" aria-label="<?php echo e(__('Login')); ?>">
                    <?php echo csrf_field(); ?>

                    <h5>Correo electrónico</h5>
                    <div class="form-group has-feedback">
                        <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>"
                            placeholder="Ingrese su correo electrónico" autofocus>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        <?php if($errors->has('email')): ?>
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong><?php echo e($errors->first('email')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h5>Contraseña</h5>
                    <div class="form-group has-feedback">
                        <input id="password" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" value="<?php echo e(old('password')); ?>" 
                            placeholder="Ingrese su contraseña" autofocus>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        <?php if($errors->has('password')): ?>
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong><?php echo e($errors->first('password')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="row" style="margin-top: 30px;">
                        <div class="col-xs-6">
                            <div class="checkbox icheck">
                                <label for="remember" style="font-weight: 600"><input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> Recordarme</label>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">INICIAR SESION</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <script src="<?php echo e(asset('assets/jquery/dist/jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/admin-lte/dist/js/adminlte.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/iCheck/icheck.min.js')); ?>"></script>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });
            });
        </script>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\mgcp\resources\views/auth/login.blade.php ENDPATH**/ ?>