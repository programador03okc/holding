@extends('mgcp.layouts.app')

@section('estilos')
    <link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('cabecera') Lista de usuarios @endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Usuarios</li>
    <li class="active">Lista</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        @include('mgcp.partials.flashmsg')
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
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{($user->activo ? "Sí" : "No")}}</td>
                            <td class="text-center">
                                <a href="{{route('mgcp.usuarios.editar',$user->id)}}" class="btn btn-primary btn-sm">
                                    Editar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src='{{ asset("assets/datatables/js/jquery.dataTables.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/js/dataTables.bootstrap.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js") }}'></script>
    <script src='{{ asset("assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js") }}'></script>

    <script src='{{ asset("mgcp/js/util.js") }}'></script>
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
                        window.location = "{{route('mgcp.usuarios.nuevo')}}";
                    }
                }]
            });
        });

    </script>
@endsection
