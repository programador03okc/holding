@extends('mgcp.layouts.app')

@section('cabecera')
Dashboard
@endsection

@section('estilos')
<link rel="stylesheet" href="{{asset('assets/Ionicons/css/ionicons.min.css')}}">
<style>
    div.info-box {
        box-shadow: none;
        border: 1px solid #ccc;
    }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{route('mgcp.home')}}">Inicio</a></li>
    <li class="active">Dashboard</li>
</ol>
@endsection

@section('cuerpo')
<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            Cuadros de presupuesto del
            <select id="selectCdpAnio">
                @foreach ($aniosCdp as $anio)
                <option value="{{$anio->anio}}">{{$anio->anio}}</option>
                @endforeach
            </select>

        </h3>
    </div>
    <div class="box-body" id="divIndicadoresCdp">
        <div class="row">
            <div class="col-sm-3" id="divCdpPendienteAprobar">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Pendientes de aprobar</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-checkmark"></i>
                    </div>
                    <a target="_blank" href="{{route('mgcp.cuadro-costos.aplicar-filtro-indicador',[1,date('Y')])}}" class="small-box-footer">Ver <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-sm-3" id="divCdpPendienteRegularizar">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Pendientes de regularizar</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-information-circled"></i>
                    </div>
                    <a target="_blank" href="{{route('mgcp.cuadro-costos.aplicar-filtro-indicador',[2,date('Y')])}}" class="small-box-footer">Ver <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-sm-3" id="divCdpDespues24h">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Con sol. aprob. dsps. 24h</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-clock"></i>
                    </div>
                    <a target="_blank" href="{{route('mgcp.ordenes-compra.propias.aplicar-filtro-indicador',[1,date('Y')])}}" class="small-box-footer">Ver <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            
            <div class="col-sm-3" id="divCdpOcSinCuadro">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>0</h3>
                        <p>O/C sin cuadro</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-grid"></i>
                    </div>
                    <a target="_blank" href="{{route('mgcp.ordenes-compra.propias.aplicar-filtro-indicador',[2,date('Y')])}}" class="small-box-footer">Ver <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Montos adjudicados de O/C directas y OCAM del <select id="selectMontosAdjudicadosOcPorAnio">
                @foreach ($aniosCdp as $anio)
                <option value="{{$anio->anio}}">{{$anio->anio}}</option>
                @endforeach
            </select> (en soles)</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12" id="divMontosAdjudicadosOrdenesPorAnio">
                <canvas id="canvaMontosAdjudicadosOrdenesPorAnio" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Ventas a terceros facturadas y metas por facturar del <select id="selectMontosFacturadosTercerosPorAnio">
                @foreach ($aniosCdp as $anio)
                <option value="{{$anio->anio}}">{{$anio->anio}}</option>
                @endforeach
            </select> (en soles)</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12" id="divMontosFacturadosTercerosPorAnio">
                <canvas id="canvaMontosFacturadosTercerosPorAnio" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!--<div class="row">
    <div class="col-sm-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Montos de O/C por vendedor</h3>
            </div>
            <div class="box-body">

                <div class="row">
                    <div class="col-md-8">
                        <div class="chart-responsive">
                            <canvas id="pieChart" height="150"></canvas>
                        </div>
                        
                    </div>
                    
                    <div class="col-md-4">
                        <ul class="chart-legend clearfix">
                            <li><i class="fa fa-circle-o text-red"></i> M. Rivera</li>
                            <li><i class="fa fa-circle-o text-green"></i> H. Medina</li>
                            <li><i class="fa fa-circle-o text-yellow"></i> J. Alfaro</li>
                            <li><i class="fa fa-circle-o text-aqua"></i> L. Reynoso</li>
                            <li><i class="fa fa-circle-o text-light-blue"></i> M. Hinostroza</li>
                            <li><i class="fa fa-circle-o text-gray"></i> B. Correa</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>-->
@endsection

@section('scripts')
<script src='{{ asset("mgcp/js/util.js") }}?v={{ filemtime(public_path("mgcp/js/util.js")) }}'></script>
<script src='{{ asset("assets/jquery-number/jquery.number.min.js") }}'></script>
<!-- ChartJS -->
<script src='{{ asset("assets/chart.js/chart.js") }}'></script>
<script src='{{ asset("assets/chart.js/chartjs-plugin-datalabels.js") }}'></script>
<!--
<script src='{{ asset("mgcp/js/demo/dashboard2.js") }}'></script>
<script src='{{ asset("mgcp/js/demo/demo.js") }}'></script>-->
<script src='{{ asset("assets/loadingoverlay/loadingoverlay.min.js") }}'></script>
<script src='{{ asset("mgcp/js/indicadores/dashboard-view.js") }}?v={{ filemtime(public_path("mgcp/js/indicadores/dashboard-view.js")) }}'></script>
<script src='{{ asset("mgcp/js/indicadores/dashboard-model.js") }}?v={{ filemtime(public_path("mgcp/js/indicadores/dashboard-model.js")) }}'></script>

<script>
    $(document).ready(function() {
        const token = '{{csrf_token()}}';
        Util.seleccionarMenu(window.location);
        const dashboardView = new DashboardView(new DashboardModel(token));
        dashboardView.mostrarIndicadorCdp();
        dashboardView.actualizarIndicadorCdpEvent();
        dashboardView.mostrarGraficoMontosAdjudicadosPorAnio();
        dashboardView.actualizarGraficoMontosAdjudicadosPorAnio();
        dashboardView.mostrarGraficoMontosFacturadosTercerosPorAnio();
    });
</script>
@endsection