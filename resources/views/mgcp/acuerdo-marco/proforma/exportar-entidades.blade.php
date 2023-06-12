@extends('mgcp.layouts.app')

@section('cabecera')
    Exportar entidades
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('mgcp.home') }}">Inicio</a></li>
        <li class="active">Acuerdo marco</li>
        <li class="active">Proformas</li>
        <li class="active">Exportar Entidades</li>
    </ol>
@endsection

@section('cuerpo')

    <div class="box box-solid">
        <div class="box-body">
            <p>El sistema generará un archivo Excel con las entidades dada la fecha de las proformas</p>
            <br>
            <form class="form-horizontal" method="POST" target="_blank" action="{{route('mgcp.acuerdo-marco.proformas.exportar.generar-entidades')}}">
                @csrf
                <div class="form-group">
                    <label class="col-sm-2 control-label">Departamento</label>
                    <div class="col-sm-2">
                        <select class="selectpicker" data-live-search="true" data-width="100%" data-actions-box="true" multiple data-size="8" name="departamento[]">
                            @foreach ($departamentos as $departamento)
                            <option selected value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">F. emisión desde</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control date-picker" name="fechaEmisionDesde" value="{{date('d-m-Y')}}">
                    </div>
                    <label class="col-sm-2 control-label">F. emisión hasta</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control date-picker" name="fechaEmisionHasta" value="{{date('d-m-Y')}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary">Exportar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<link href='{{asset("assets/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css")}}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-datepicker/dist/js/locales/bootstrap-datepicker.es.min.js") }}'></script>

<link href='{{ asset("assets/bootstrap-select/css/bootstrap-select.min.css") }}' rel="stylesheet" type="text/css" />
<script src='{{ asset("assets/bootstrap-select/js/bootstrap-select.min.js") }}'></script>
<script src='{{ asset("assets/bootstrap-select/js/i18n/defaults-es_ES.js") }}'></script>

    <script src="{{ asset('mgcp/js/util.js') }}"></script>
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
            Util.activarDatePicker();
        });

    </script>
@endsection
