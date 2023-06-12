@extends('mgcp.layouts.app')
@section('contenido')

@section('cabecera')
Tipo de Componentes
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Acuerdo marco</li>
    <li class="active">Publicar</li>
    <li class="active">Tipo de Componentes</li>
</ol>
@endsection


@section('cuerpo')
<div class="box box-solid">
    <div class="box-body">
        <div class="row">
            <div class="col-md-5">
                <form id="formulario">
                    @csrf

                    <div class="form-group">
                        <h6>Descripción del tipo de componente</h6>
                        <input type="text" name="descripcion" class="form-control input-sm" placeholder="Nombre o descripción del tipo de componente">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-pill btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <div class="col-md-7">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-hover" id="tablaComponente">
                        <thead>
                            <th>#</th>
                            <th>Descripción</th>
                        </thead>
                    </table>
                </div>
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

    <script src="{{ asset('assets/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap-select/js/i18n/defaults-es_ES.min.js') }}"></script>

    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
    <script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

    <script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
    <script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>

    <script src='{{ asset("mgcp/js/util.js?v=27") }}'></script>
    <script src='{{ asset("mgcp/js/moment.min.js?v=1") }}'></script>
    <script src="{{ asset('mgcp/js/acuerdo-marco/proforma/componente/tipo-componente.js?v=1') }}"></script>
    <script>
    $(document).ready(function () {
        Util.seleccionarMenu(window.location);
        const token = '{{ csrf_token() }}';
    });
    </script>
@endsection