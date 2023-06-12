<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('cabecera') - Módulo de Gestión Comercial</title>
    <link rel="shortcut icon" href="{{ asset('mgcp/img/mgc.ico') }}" />
    <link rel="stylesheet" href="{{asset('assets/bootstrap/dist/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin-lte/dist/css/AdminLTE.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin-lte/dist/css/skins/skin-blue.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/sweetalert/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('mgcp/css/app.css?v=14')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    @yield('estilos')
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        @include("mgcp/layouts/header")
        @include("mgcp/layouts/aside")
        
        <div class="content-wrapper">
            <section class="content-header">
                <h1>@yield('cabecera')</h1>
                @yield('breadcrumb')
            </section>
            <section class="content">
                @yield('cuerpo')
            </section>
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs"></div>Copyright &copy; 2022 Módulo de Gestión Comercial
        </footer>

        <div class="control-sidebar-bg"></div>
    </div>
    
    <script src='{{asset("assets/jquery/dist/jquery.min.js")}}'></script>
    <script src='{{asset("assets/bootstrap/dist/js/bootstrap.min.js")}}'></script>
    <script src='{{asset("assets/admin-lte/dist/js/adminlte.min.js")}}'></script>
    <script src='{{asset("mgcp/js/notificacion-model.js")}}'></script>
    <script src='{{asset("mgcp/js/notificacion-view.js")}}'></script>
    <script src='{{asset("mgcp/js/orden-compra/propia/oc-propia-indicador-view.js?v=3")}}'></script>
    <script src='{{asset("mgcp/js/orden-compra/propia/oc-propia-indicador-model.js?v=3")}}'></script>
    
    <script src='{{asset("assets/sweetalert/sweetalert2.min.js")}}'></script>
    @routes
    <script>
        $(document).ready(function() {
            const token = '{{csrf_token()}}';
            const notificacionView = new NotificacionView(new NotificacionModel(token));
            //const paqueteView= new PaqueteView(new PaqueteModel(token));
            //paqueteView.obtenerCantidadPendientes();

            notificacionView.obtenerNoLeidas();
            //setInterval(notificacionView.obtenerNoLeidas, 300000);

            if ("{{ Auth::user()->tieneRol(125) }}" == "1") {
                const indicadorView = new OcPropiaIndicadorView(new OcPropiaIndicadorModel(token));
                indicadorView.obtenerIndicadorDiario();
                indicadorView.obtenerIndicadorMensual();
                //setInterval(indicadorView.obtenerIndicadorDiario, 600000);
                //setInterval(indicadorView.obtenerIndicadorMensual, 600000);
            }
        });
    </script>
    @yield('scripts')
</body>

</html>