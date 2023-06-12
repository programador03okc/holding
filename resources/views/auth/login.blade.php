<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Iniciar sesión - MGC</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="{{ asset('mgcp/img/mgc.ico') }}" />
        <link rel="stylesheet" href="{{ asset('assets/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin-lte/dist/css/AdminLTE.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/iCheck/square/blue.css') }}">
        <link rel="stylesheet" href="{{ asset('mgcp/css/app.css') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            @include('mgcp.partials.flashmsg')
            <div class="login-box-body">
                <div class="login-box-logo">
                    <img src="{{ asset('mgcp/img/mgc_logo.png') }}" alt="">
                </div>
                <p class="login-box-msg">¡Bienvenido al Módulo de Gestión Comercial, ingrese sus credenciales!</p>

                <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                    @csrf

                    <h5>Correo electrónico</h5>
                    <div class="form-group has-feedback">
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}"
                            placeholder="Ingrese su correo electrónico" autofocus>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <h5>Contraseña</h5>
                    <div class="form-group has-feedback">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password') }}" 
                            placeholder="Ingrese su contraseña" autofocus>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback text-danger" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="row" style="margin-top: 30px;">
                        <div class="col-xs-6">
                            <div class="checkbox icheck">
                                <label for="remember" style="font-weight: 600"><input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme</label>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">INICIAR SESION</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <script src="{{ asset('assets/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/admin-lte/dist/js/adminlte.min.js') }}"></script>
        <script src="{{ asset('assets/iCheck/icheck.min.js') }}"></script>
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
