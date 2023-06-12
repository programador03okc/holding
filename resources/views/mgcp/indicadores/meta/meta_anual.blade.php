@extends('mgcp.layouts.app')

@section('cabecera') Registro de Metas Comerciales @endsection

@section('cuerpo')
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Registro de Metas</h3></div>
            <form id="formulario" role="form">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="form-group">
                                <h6>Periodo</h6>
                                <select class="form-control input-sm" name="periodo">
                                    @foreach ($periodo as $itemPeriodo)
                                        <option value="{{ $itemPeriodo->id_periodo }}">{{ $itemPeriodo->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h6>Meta Q1</h6>
                        <input type="number" class="form-control input-sm text-center" name="q1" value="0.00" step="any" min="0"
                        onkeyup="calculate(this.value, 'q1');" onclick="calculate(this.value, 'q1');">
                    </div>
                    <div class="form-group">
                        <h6>Meta Q2</h6>
                        <input type="number" class="form-control input-sm text-center" name="q2" value="0.00" step="any" min="0"
                        onkeyup="calculate(this.value, 'q2');" onclick="calculate(this.value, 'q2');">
                    </div>
                    <div class="form-group">
                        <h6>Meta Q3</h6>
                        <input type="number" class="form-control input-sm text-center" name="q3" value="0.00" step="any" min="0"
                        onkeyup="calculate(this.value, 'q3');" onclick="calculate(this.value, 'q3');">
                    </div>
                    <div class="form-group">
                        <h6>Meta Q4</h6>
                        <input type="number" class="form-control input-sm text-center" name="q4" value="0.00" step="any" min="0"
                        onkeyup="calculate(this.value, 'q4');" onclick="calculate(this.value, 'q4');">
                    </div>
                    <div class="form-group">
                        <h6>Meta Anual</h6>
                        <input type="number" class="form-control input-sm text-center" name="anual" value="0.00" step="any" min="0" readonly>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success btn-sm btn-block btn-flat">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-9">
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Historial de Metas</h3></div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Meta Q1</th>
                                <th>Meta Q2</th>
                                <th>Meta Q3</th>
                                <th>Meta Q4</th>
                                <th>Meta Anual</th>
                            </tr>
                        </thead>
                        <body>
                            <tbody>
                                <td colspan="6">No se encontraron resultados</td>
                            </tbody>
                        </body>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src='{{ asset("assets/lobibox/dist/js/lobibox.min.js") }}'></script>
    <script src='{{ asset("mgcp/js/util.js?v=27") }}'></script>    
    <script>
        $(document).ready(function() {
            Util.seleccionarMenu(window.location);
        });

        function calculate(value, type) {
            var q1 = 0, q2 = 0, q3 = 0, q4 = 0;
            if (type == 'q1') {
                q1 = value;
                q2 = $("[name=q2]").val();
                q3 = $("[name=q3]").val();
                q4 = $("[name=q4]").val();
            } else if (type == 'q2') {
                q1 = $("[name=q1]").val();
                q2 = value;
                q3 = $("[name=q3]").val();
                q4 = $("[name=q4]").val();
            } else if (type == 'q3') {
                q1 = $("[name=q1]").val();
                q2 = $("[name=q2]").val();
                q3 = value;
                q4 = $("[name=q4]").val();
            } else if (type == 'q4') {
                q1 = $("[name=q1]").val();
                q2 = $("[name=q2]").val();
                q3 = $("[name=q3]").val();
                q4 = value;
            }
            var total = parseFloat(q1) + parseFloat(q2) + parseFloat(q3) + parseFloat(q4)
            $("[name=anual]").val(total.toFixed(2));
        }
    </script>
@endsection