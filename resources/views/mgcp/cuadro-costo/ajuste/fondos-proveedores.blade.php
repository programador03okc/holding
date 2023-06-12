@extends('mgcp.layouts.app')

@section('cabecera')
Fondos de proveedores
@endsection

@section('estilos')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Cuadros de presupuesto</li>
    <li class="active">Ajustes</li>
    <li class="active">Fondos de proveedores</li>
</ol>
@endsection

@section('cuerpo')

<div class="box box-solid">
    <div class="box-body">
        <div class="table-responsive">
            <table id="tableFondos" style="width: 100%" class="table table-condensed table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 25%">Descripción</th>
                        <th class="text-center">Valor unitario</th>
                        <th class="text-center">Cantidad ingresada</th>
                        <th class="text-center">Cantidad utilizada</th>
                        <th class="text-center">Cantidad disponible</th>
                        <th class="text-center">Subtotal disponible</th>
                        <th style="width: 5%" class="text-center">Activo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="modal fade" id="modalNuevoFondo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo fondo</h4>
            </div>
            <div class="modal-body">
                <form id="formNuevoFondo">
                    @csrf
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" class="form-control" required name="descripcion" id="txtDescripcion" placeholder="Descripción">
                    </div>
                    <div class="form-group">
                        <label>Valor unitario</label>
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span id="spanMonedaSeleccionada">S/</span> <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a class="moneda" href="#">S/</a></li>
                                    <li><a class="moneda" href="#">$</a></li>
                                </ul>
                                <input type="hidden" id="txtMoneda" name="moneda" value="s">
                            </div>
                            <input type="text" class="form-control decimal" id="txtValorUnitario" name="valor" placeholder="Valor unitario">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">Cantidad inicial</label>
                        <input type="number" required class="form-control entero" id="txtCantidadInicial" name="cantidad_inicial" placeholder="Cantidad inicial">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnRegistrarFondo">Registrar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalListaCantidadesIngresadas" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Lista de cantidades ingresadas</h4>
            </div>
            <div class="modal-body">
                <p>Fondo: <span class="fondo"></span></p>
                <table id="tableCantidadesIngresadas" style="width: 100%" class="table table-condensed table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Registrado por</th>
                            <th class="text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoIngreso" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo ingreso</h4>
            </div>
            <div class="modal-body">
                <div>
                    <div class="form-group">
                        <label>Fondo</label>
                        <div class="form-control-static fondo">Fondo Microsoft</div>
                    </div>
                    <div class="form-group">
                        <label>Cantidad actual</label>
                        <div class="form-control-static cantidad">20</div>
                    </div>
                    <div class="form-group">
                        <label>Cantidad adicional</label>
                        <input type="number" class="form-control" id="txtNuevoIngreso" placeholder="Cantidad adicional">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnNuevoIngresoRegistrar">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalListaCantidadesUtilizadas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Lista de cantidades utilizadas</h4>
            </div>
            <div class="modal-body">
                <p>Fondo: <span class="fondo"></span></p>
                <table id="tableCantidadesUtilizadas" style="width: 100%; font-size: small" class="table table-hover table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Tipo cuadro</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Estado cuadro</th>
                            <th class="text-center">Descripción de producto</th>
                            <th class="text-center">Proveedor seleccionado</th>
                            <th class="text-center">Comentario</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Monto</th>
                            <th class="text-center">Comprado</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCambiarEstadoFondo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span class="accion">Titulo</span> fondo</h4>
            </div>
            <div class="modal-body">
                <p>Fondo: <span class="fondo"></span></p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary accion" data-id="0" id="btnCambiarEstadoFondo">Accion</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')

<link href="{{ asset('assets/datatables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/datatables/extensions/Buttons/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/datatables/extensions/Buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/datatables/extensions/Buttons/js/buttons.bootstrap.min.js') }}"></script>
<link href="{{asset('assets/lobibox/dist/css/lobibox.min.css')}}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/lobibox/dist/js/lobibox.min.js') }}"></script>
<script src="{{ asset('assets/loadingoverlay/loadingoverlay.min.js') }}"></script>

<script src="{{ asset('mgcp/js/cuadro-costos/fondo-proveedor-view.js?v=15') }}"></script>
<script src="{{ asset('mgcp/js/cuadro-costos/fondo-proveedor-model.js?v=15') }}"></script>

<script src="{{ asset('mgcp/js/util.js?v=2') }}"></script>


<script>
    $(document).ready(function() {

        Util.seleccionarMenu(window.location);
        Util.activarSoloEnteros();
        Util.activarSoloDecimales();
        const token = '{{csrf_token()}}';
        const fondo = new FondoProveedorView(new FondoProveedorModel(token));
        fondo.listar();
        fondo.nuevoEvent();
        fondo.cantidadIngresadaEvent();
        fondo.cantidadUtilizadaEvent();
        fondo.cambiarEstadoEvent();
        fondo.nuevoIngresoEvent();
    });

</script>
@endsection
