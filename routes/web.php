<?php

use App\Http\Controllers\Finanzas\CentroCosto\CentroCostoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Descarga\NuevoProductoController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Descarga\ProductoAdjudicadoController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Descarga\ProformaController;
use App\Http\Controllers\mgcp\AcuerdoMarco\EmpresaController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Entidad\ContactoEntidadController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Entidad\EntidadController;
use App\Http\Controllers\mgcp\AcuerdoMarco\PeruComprasController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Producto\CatalogoController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Producto\CategoriaController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Producto\EquipoComputoController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Producto\HistorialActualizacionController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Producto\ProductoController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CalculadoraProductoController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria\COINuevaVistaController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria\COIVistaAnteriorController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria\CompraOrdinariaIndividualController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\CompraOrdinaria\CompraOrdinariaPaqueteController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\GranCompra\GCINuevaVistaController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\GranCompra\GCIVistaAnteriorController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\GranCompra\GranCompraIndividualController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\GranCompra\GranCompraPaqueteController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ProformaAnalisisController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ProformaIndividualController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ProformaPaqueteController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\NuevoPrecioController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\PlazoEntregaController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\StockEmpresaController;
use App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\StockProductoController;
use App\Http\Controllers\mgcp\AcuerdoMarco\TransportistaController;
use App\Http\Controllers\mgcp\CuadroCosto\Ajuste\AprobadorController;
use App\Http\Controllers\mgcp\CuadroCosto\Ajuste\FondoMicrosoftController;
use App\Http\Controllers\mgcp\CuadroCosto\Ajuste\FondoProveedorController;
use App\Http\Controllers\mgcp\CuadroCosto\Ajuste\LicenciaController;
use App\Http\Controllers\mgcp\CuadroCosto\Ajuste\TipoCambioController;
use App\Http\Controllers\mgcp\CuadroCosto\CcAmController;
use App\Http\Controllers\mgcp\CuadroCosto\CcBsController;
use App\Http\Controllers\mgcp\CuadroCosto\CcGgController;
use App\Http\Controllers\mgcp\CuadroCosto\CuadroCostoController;
use App\Http\Controllers\mgcp\CuadroCosto\MovimientoTransformacionController;
use App\Http\Controllers\mgcp\CuadroCosto\ProveedorController;
use App\Http\Controllers\mgcp\CuadroCosto\Reporte\PendienteCierreController;
use App\Http\Controllers\mgcp\CuadroCosto\ResponsableController;
use App\Http\Controllers\mgcp\CuadroCosto\SolicitudController;
use App\Http\Controllers\mgcp\Indicadores\DashboardController;
use App\Http\Controllers\mgcp\Integracion\ProductoCeam;
use App\Http\Controllers\mgcp\OportunidadController;
use App\Http\Controllers\mgcp\OrdenCompra\Propia\DespachoController;
use App\Http\Controllers\mgcp\OrdenCompra\Propia\IndicadorController;
use App\Http\Controllers\mgcp\OrdenCompra\Propia\OrdenCompraDirectaController;
use App\Http\Controllers\mgcp\OrdenCompra\Propia\OrdenCompraAmController;
use App\Http\Controllers\mgcp\OrdenCompra\Propia\OrdenCompraPropiaController;
use App\Http\Controllers\mgcp\PerfilController;
use App\Http\Controllers\mgcp\TesterController;
use App\Http\Controllers\mgcp\Usuario\Logs\ActividadUsuarioController;
use App\Http\Controllers\mgcp\Usuario\Logs\InicioSesionController;
use App\Http\Controllers\mgcp\UsuarioController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

require __DIR__ . '/auth.php';
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('artisan', function () {
    Artisan::call('clear-compiled');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
});

/**
 * RUTAS TEST
 */
Route::get('/test-proforma', [ProformaAnalisisController::class, 'testProforma'])->name('test-proforma');
Route::get('/test-proforma-analisis/{type}/{return}', [ProformaAnalisisController::class, 'filtroAnalisisProformas'])->name('test-proforma-analisis');
Route::get('/test-validar-proforma', [ProformaAnalisisController::class, 'validarProforma'])->name('test-validar-proforma');
Route::get('test-stock', [StockProductoController::class, 'testStock'])->name('test-stock');
Route::post('test-plazos', [PlazoEntregaController::class, 'testPlazos'])->name('test-plazos');
Route::get('test-productos', [ProductoController::class, 'testProductos'])->name('test-productos');

Route::name('testing.')->prefix('testing')->middleware('auth')->group(function () {
    Route::get('oportunidades', [TesterController::class, 'oportunidades'])->name('oportunidades');
    Route::get('codigo-oportunidades', [TesterController::class, 'codigoOportunidades'])->name('codigo-oportunidades');
});

/**
 * RUTAS EN PRODUCCION
 */
Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/login', function () { return Socialite::driver('microsoft')->redirect(); })->name('login');
//Route::get('/validar-login', [UsuarioController::class, 'validarLogin'])->name('validarLogin');
Route::get('/logout', [UsuarioController::class, 'logout'])->name('logout');
Route::get('/home', [HomeController::class, 'index'])->name('home2');

Route::get('/replicar-cuadro-costo-total', [CuadroCostoController::class, 'replicarCuadroCostoTotal'])->name('replicar-cuadro-costo-total');
Route::get('/replicar-cuadro-costo-id/{id}', [CuadroCostoController::class, 'replicarCuadroCostoId'])->name('replicar-cuadro-costo-id');
Route::get('/replicar-proformas', [ProformaAnalisisController::class, 'replicarProformas'])->name('replicar-proformas');
Route::get('/api-crm', [HomeController::class, 'apiCRM'])->name('api-crm');


Route::name('finanzas.')->prefix('finanzas')->middleware('auth')->group(function () {
    Route::name('centro-costos.')->prefix('centro-costos')->group(function () {
        Route::post('mostrar', [CentroCostoController::class, 'mostrarCentroCostosSegunGrupoUsuario'])->name('mostrar');
    });
});

Route::name('mgcp.')->prefix('mgcp')->group(function () { //Route::group(['as' => 'mgcp.', 'prefix' => 'mgcp'], function () {
    Route::get('/', 'HomeController@index')->middleware('auth')->name('base');
    Route::get('home', [HomeController::class, 'index'])->middleware('auth')->name('home');

    Route::name('automatizar.')->prefix('automatizar')->group(function () {
        Route::get('eliminar-precios-calculadora', [CalculadoraProductoController::class, 'eliminarPrecios'])->name('eliminar-precios-calculadora');
        Route::name('descargas.')->prefix('descargas')->group(function () { //Route::group(['as' => 'descarga-automatica.', 'prefix' => 'descarga-automatica'], function () {
            Route::get('proformas/{idEmpresa}/{diasAntiguedad}', [ProformaController::class, 'descargaAutomatica'])->name('proformas');
            Route::get('semaforo-empresa/{idEmpresa}', [EmpresaController::class, 'obtenerSemaforo'])->name('semaforo-empresa');
            Route::get('notificaciones-acuerdo-marco/{tipo}', [App\Http\Controllers\mgcp\AcuerdoMarco\NotificacionController::class, 'actualizarLista'])->name('notificaciones-acuerdo-marco');
        });
        Route::name('publicar.')->prefix('publicar')->group(function () {
            Route::get('stock-por-proformas/{idEmpresa}', [StockProductoController::class, 'publicarStockPorProformas'])->name('stock');
        });
        Route::get('proformas-individual-masiva', [COINuevaVistaController::class, 'generarFletePorLoteMasivo'])->name('proformas-individual-masiva');
        Route::get('descargar-proveedores-portal/{id}', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'proveedoresPortal'])->name('descargar-proveedores-portal');
        Route::get('ceam', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'ceamPortal'])->name('ceam');
        Route::get('stock-cero-empresa', [StockEmpresaController::class, 'stockCeroEmpresa'])->name('stock-cero-empresa');
    });

    Route::name('perfil.')->prefix('perfil')->middleware('auth')->group(function () { //Route::group(['as' => 'perfil.', 'prefix' => 'perfil', 'middleware' => 'auth'], function () {
        Route::get('cambiar-password', [PerfilController::class, 'cambiarPassword'])->name('cambiar-password');
        Route::post('actualizar-password', [PerfilController::class, 'actualizarPassword'])->name('actualizar-password');
        Route::get('cambiar-foto', [PerfilController::class, 'cambiarFoto'])->name('cambiar-foto');
    });

    Route::name('notificaciones.')->prefix('notificaciones')->middleware('auth')->group(function () { //Route::group(['as' => 'notificaciones.', 'prefix' => 'notificaciones', 'middleware' => 'auth'], function () {
        Route::get('lista', [App\Http\Controllers\mgcp\NotificacionController::class, 'lista'])->name('lista');
        Route::get('ver/{id}', [App\Http\Controllers\mgcp\NotificacionController::class, 'ver'])->name('ver');
        Route::post('data-lista', [App\Http\Controllers\mgcp\NotificacionController::class, 'dataLista'])->name('data-lista');
        Route::post('eliminar', [App\Http\Controllers\mgcp\NotificacionController::class, 'eliminar'])->name('eliminar');
        Route::post('cantidad-no-leidas', [App\Http\Controllers\mgcp\NotificacionController::class, 'cantidadNoLeidas'])->name('cantidad-no-leidas');
    });

    Route::name('usuarios.')->prefix('usuarios')->middleware('auth')->group(function () { //Route::group(['as' => 'usuarios.', 'prefix' => 'usuarios', 'middleware' => 'auth'], function () {
        Route::get('lista', [UsuarioController::class, 'lista'])->name('lista');
        Route::get('editar/{id}', [UsuarioController::class, 'editar'])->name('editar');
        Route::post('actualizar', [UsuarioController::class, 'actualizar'])->name('actualizar');
        Route::get('nuevo', [UsuarioController::class, 'nuevo'])->name('nuevo');
        Route::post('registrar', [UsuarioController::class, 'registrar'])->name('registrar');
        // Route::get('logout', [UsuarioController::class, 'logout'])->name('logout');
        Route::post('renovar-clave', [UsuarioController::class, 'renovarClave'])->name('renovar-clave');

        Route::name('logs.')->prefix('logs')->group(function () { //Route::group(['as' => 'usuarios.', 'prefix' => 'usuarios', 'middleware' => 'auth'], function () {
            Route::name('inicios-sesion.')->prefix('inicios-sesion')->group(function () { //Route::group(['as' => 'usuarios.', 'prefix' => 'usuarios', 'middleware' => 'auth'], function () {
                Route::get('index', [InicioSesionController::class, 'index'])->name('index');
                Route::post('data-lista', [InicioSesionController::class, 'dataLista'])->name('data-lista');
            });
            Route::name('actividades-usuario.')->prefix('actividades-usuario')->group(function () { //Route::group(['as' => 'usuarios.', 'prefix' => 'usuarios', 'middleware' => 'auth'], function () {
                Route::get('index', [ActividadUsuarioController::class, 'index'])->name('index');
                Route::post('data-lista', [ActividadUsuarioController::class, 'dataLista'])->name('data-lista');
            });
        });
    });

    Route::name('ordenes-compra.')->prefix('ordenes-compra')->middleware('auth')->group(function () {
        Route::name('propias.')->prefix('propias')->group(function () {
            Route::get('aplicar-filtro-indicador/{tipo}/{anio}', [OrdenCompraPropiaController::class, 'aplicarFiltroIndicador'])->name('aplicar-filtro-indicador');
            Route::get('lista', [OrdenCompraPropiaController::class, 'lista'])->name('lista');
            Route::post('data-lista', [OrdenCompraPropiaController::class, 'dataLista'])->name('data-lista');
            Route::post('actualizar-filtros', [OrdenCompraPropiaController::class, 'actualizarFiltros'])->name('actualizar-filtros');
            Route::post('exportar-lista', [OrdenCompraPropiaController::class, 'exportarLista'])->name('exportar-lista');
            Route::post('obtener-informacion-adicional', [OrdenCompraPropiaController::class, 'obtenerInformacionAdicional'])->name('obtener-informacion-adicional')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class,'auth']);
            Route::post('cambiar-contacto', [OrdenCompraPropiaController::class, 'cambiarContacto'])->name('cambiar-contacto');
            Route::post('actualizar-campo', [OrdenCompraPropiaController::class, 'actualizarCampo'])->name('actualizar-campo');
            Route::post('cambiar-despacho', [OrdenCompraPropiaController::class, 'cambiarDespacho'])->name('cambiar-despacho');
            Route::post('vincular-oportunidad', [OrdenCompraPropiaController::class, 'vincularOportunidad'])->name('vincular-oportunidad');

            Route::name('comentarios.')->prefix('comentarios')->group(function () { //Route::group(['as' => 'comentarios.', 'prefix' => 'comentarios'], function () {
                Route::post('registrar', [App\Http\Controllers\mgcp\OrdenCompra\Propia\ComentarioController::class, 'registrar'])->name('registrar');
                Route::post('listar-por-oc', [App\Http\Controllers\mgcp\OrdenCompra\Propia\ComentarioController::class, 'listarPorOc'])->name('listar-por-oc');
                Route::post('eliminar', [App\Http\Controllers\mgcp\OrdenCompra\Propia\ComentarioController::class, 'eliminar'])->name('eliminar');
            });

            Route::name('despachos.')->prefix('despachos')->group(function () { //Route::group(['as' => 'comentarios.', 'prefix' => 'comentarios'], function () {
                Route::post('obtener-detalles', [DespachoController::class, 'obtenerDetalles'])->name('obtener-detalles');
                Route::post('actualizar', [DespachoController::class, 'actualizar'])->name('actualizar');
                //Route::post('listar-por-oc', [App\Http\Controllers\mgcp\OrdenCompra\Propia\ComentarioController::class, 'listarPorOc'])->name('listar-por-oc');
                //Route::post('eliminar', [App\Http\Controllers\mgcp\OrdenCompra\Propia\ComentarioController::class, 'eliminar'])->name('eliminar');
            });

            Route::name('directas.')->prefix('directas')->group(function () {
                Route::get('nueva', [OrdenCompraDirectaController::class, 'nueva'])->name('nueva');
                Route::post('registrar', [OrdenCompraDirectaController::class, 'registrar'])->name('registrar');
                Route::get('descargar-archivo/{id}/{archivo}', [OrdenCompraDirectaController::class, 'descargarArchivo'])->name('descargar-archivo')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class,'auth']);
            });

            Route::name('acuerdo-marco.')->prefix('acuerdo-marco')->group(function () {
                Route::post('descargar-desde-portal', [OrdenCompraAmController::class, 'descargarDesdePortal'])->name('descargar-desde-portal');
                Route::post('obtener-productos', [OrdenCompraAmController::class, 'obtenerProductos'])->name('obtener-productos');
                Route::post('actualizar-fecha-descarga-empresa', [OrdenCompraAmController::class, 'actualizarFechaDescargaEmpresa'])->name('actualizar-fecha-descarga-empresa');
                Route::post('obtener-fecha-descarga-empresa', [OrdenCompraAmController::class, 'obtenerFechaDescargaEmpresa'])->name('obtener-fecha-descarga-empresa');
            });

            Route::name('indicadores.')->prefix('indicadores')->group(function () {
                Route::post('obtener-indicador-diario', [IndicadorController::class, 'obtenerIndicadorDiario'])->name('obtener-indicador-diario');
                Route::post('obtener-indicador-mensual', [IndicadorController::class, 'obtenerIndicadorMensual'])->name('obtener-indicador-mensual');
                Route::get('configuracion', [IndicadorController::class, 'configuracion'])->name('configuracion');
                Route::post('actualizar-configuracion', [IndicadorController::class, 'actualizarConfiguracion'])->name('actualizar-configuracion');
            });
        });
        Route::name('publicas.')->prefix('publicas')->group(function () {
            Route::get('lista', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'lista'])->name('lista');
            Route::post('data-lista', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'dataLista'])->name('data-lista');
            Route::post('obtener-estados-portal', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'obtenerEstadosPortal'])->name('obtener-estados-portal');
            Route::post('actualizar-filtros', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'actualizarFiltros'])->name('actualizar-filtros');
            Route::post('obtener-ordenes-por-producto', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'obtenerOrdenesPorProducto'])->name('obtener-ordenes-por-producto');
            Route::post('obtener-detalles-portal', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'obtenerDetallesPortal'])->name('obtener-detalles-portal');
			
            //// POR ELIMINAR
			Route::name('analisis-ocp.')->prefix('analisis-ocp')->group(function () {
                Route::get('lista', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'listaOrdenesPublicasAnalisis'])->name('lista');
                Route::post('data-lista', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'dataListaAnalisis'])->name('data-lista');
                Route::post('registrar', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'registrar'])->name('registrar');
                Route::post('editar', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'editar'])->name('editar');
                Route::post('busqueda-producto', [ProductoController::class, 'busquedaProductoPN'])->name('busqueda-producto');
                //Route::get('api', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'proveedoresPortal'])->name('api');
                Route::get('exportar', [App\Http\Controllers\mgcp\OrdenCompra\Publica\OrdenCompraPublicaController::class, 'exportar'])->name('exportar');
            });
        });
    });

    Route::name('cuadro-costos.')->prefix('cuadro-costos')->middleware('auth')->group(function () { //Route::group(['as' => 'cuadro-costos.', 'prefix' => 'cuadro-costos', 'middleware' => 'auth'], function () {
        Route::get('detalles/{id?}', [CuadroCostoController::class, 'detalles'])->name('detalles'); //Id es marcado como opcional para poder imprimir la URL sin id en las vistas
        Route::post('seleccionar-centro-costo', [CuadroCostoController::class, 'seleccionarCentroCosto'])->name('seleccionar-centro-costo');
        Route::get('crear-desde-orden-compra/orden/{idOrden}/oportunidad/{idOportunidad}', [CuadroCostoController::class, 'crearDesdeOrdenCompra'])->name('crear-desde-orden-compra');
        Route::get('lista', [CuadroCostoController::class, 'lista'])->name('lista');
        Route::get('aplicar-filtro-indicador/{tipo}/{anio}', [CuadroCostoController::class, 'aplicarFiltroIndicador'])->name('aplicar-filtro-indicador');
        Route::post('data-lista', [CuadroCostoController::class, 'dataLista'])->name('data-lista');
        Route::get('exportar-lista', [CuadroCostoController::class, 'exportarLista'])->name('exportar-lista');
        Route::post('actualizar-campo', [CuadroCostoController::class, 'actualizarCampo'])->name('actualizar-campo');
        Route::post('actualizar-condicion-credito', [CuadroCostoController::class, 'actualizarCondicionCredito'])->name('actualizar-condicion-credito');
        Route::post('cambiar-cuadro', [CuadroCostoController::class, 'cambiarCuadro'])->name('cambiar-cuadro');
        Route::post('finalizar', [CuadroCostoController::class, 'finalizar'])->name('finalizar');
        Route::post('actualizar-filtros', [CuadroCostoController::class, 'actualizarFiltros'])->name('actualizar-filtros');
        Route::post('obtener-detalles-filas', [CuadroCostoController::class, 'obtenerDetallesFilas'])->name('obtener-detalles-filas');
        Route::post('enviar-orden-despacho', [CuadroCostoController::class, 'enviarOrdenDespacho'])->name('enviar-orden-despacho');

        Route::name('proveedores.')->prefix('proveedores')->group(function () {
            Route::post('registrar', [ProveedorController::class, 'registrar'])->name('registrar');
        });

        Route::name('reportes.')->prefix('reportes')->group(function () {
            Route::name('pendientes-cierre.')->prefix('pendientes-cierre')->group(function () { //Route::group(['as' => 'tipo-cambio.', 'prefix' => 'tasa-cambio'], function () {
                Route::get('index', [PendienteCierreController::class, 'index'])->name('index');
                Route::post('generar-archivo', [PendienteCierreController::class, 'generarArchivo'])->name('generar-archivo');
            });
        });

        Route::name('ajustes.')->prefix('ajustes')->group(function () { //Route::group(['as' => 'ajustes.', 'prefix' => 'ajustes'], function () {
            Route::name('tipo-cambio.')->prefix('tipo-cambio')->group(function () { //Route::group(['as' => 'tipo-cambio.', 'prefix' => 'tasa-cambio'], function () {
                Route::get('index', [TipoCambioController::class, 'index'])->name('index');
                Route::post('actualizar', [TipoCambioController::class, 'actualizar'])->name('actualizar');
				Route::post('obtener-tc', [TipoCambioController::class, 'obtenerTipoCambioSbs'])->name('obtener-tc');
            });

            Route::name('fondos-proveedores.')->prefix('fondos-proveedores')->group(function () { //Route::group(['as' => 'fondos-proveedores.', 'prefix' => 'fondos-proveedores'], function () {
                Route::get('index', [FondoProveedorController::class, 'index'])->name('index');
                Route::post('data-lista', [FondoProveedorController::class, 'dataLista'])->name('data-lista');
                Route::post('data-lista-para-proformas', [FondoProveedorController::class, 'dataListaParaProformas'])->name('data-lista-para-proformas');
                Route::post('registrar-fondo', [FondoProveedorController::class, 'registrarFondo'])->name('registrar-fondo');
                Route::post('registrar-ingreso', [FondoProveedorController::class, 'registrarIngreso'])->name('registrar-ingreso');
                Route::post('cambiar-estado', [FondoProveedorController::class, 'cambiarEstado'])->name('cambiar-estado');
                Route::post('listar-ingresos-fondo', [FondoProveedorController::class, 'listarIngresosFondo'])->name('listar-ingresos-fondo');
                Route::post('listar-utilizados-fondo', [FondoProveedorController::class, 'listarUtilizadosFondo'])->name('listar-utilizados-fondo');
                Route::post('obtener-fondos-disponibles', [FondoProveedorController::class, 'obtenerFondosDisponibles'])->name('obtener-fondos-disponibles');
            });

            Route::name('fondos-microsoft.')->prefix('fondos-microsoft')->group(function () {
                Route::get('index', [FondoMicrosoftController::class, 'index'])->name('index');
                Route::get('listar', [FondoMicrosoftController::class, 'listar'])->name('listar');
                Route::post('registrar-fondo', [FondoMicrosoftController::class, 'registrarFondo'])->name('registrar-fondo');
                Route::post('listar-combo', [FondoMicrosoftController::class, 'listarCombo'])->name('listar-combo');
                Route::post('registrar-bolsa', [FondoMicrosoftController::class, 'registrarBolsa'])->name('registrar-bolsa');
                Route::post('registrar-movimiento', [FondoMicrosoftController::class, 'registrarMovimiento'])->name('registrar-movimiento');
            });

            Route::name('aprobadores.')->prefix('aprobadores')->group(function () { //Route::group(['as' => 'aprobadores.', 'prefix' => 'aprobadores'], function () {
                Route::get('index', [AprobadorController::class, 'index'])->name('index');
                Route::post('data-lista', [AprobadorController::class, 'dataLista'])->name('data-lista');
                Route::post('actualizar-aprobador-fuera-monto', [AprobadorController::class, 'actualizarAprobadorFueraMonto'])->name('actualizar-aprobador-fuera-monto');
                Route::post('eliminar-aprobador-por-monto', [AprobadorController::class, 'eliminarAprobadorPorMonto'])->name('eliminar-aprobador-por-monto');
                Route::post('registrar-aprobador-por-monto', [AprobadorController::class, 'registrarAprobadorPorMonto'])->name('registrar-aprobador-por-monto');
                Route::post('actualizar-aprobador-por-monto', [AprobadorController::class, 'actualizarAprobadorPorMonto'])->name('actualizar-aprobador-por-monto');
                Route::post('detalles-aprobador-por-monto', [AprobadorController::class, 'detallesAprobadorPorMonto'])->name('detalles-aprobador-por-monto');
            });

            Route::name('licencias.')->prefix('licencias')->group(function () {
                Route::get('index', [LicenciaController::class, 'index'])->name('index');
                Route::post('listar', [LicenciaController::class, 'listar'])->name('listar');
                Route::post('guardar', [LicenciaController::class, 'guardar'])->name('guardar');
            });

            /*Route::name('gastos.')->prefix('gastos')->group(function () {//Route::group(['as' => 'gastos.', 'prefix' => 'gastos'], function () {
                Route::get('lista', [GastosController::class, 'index'])->name('lista');
                
                Route::get('lista', array(
                    'as' => 'lista',
                    'uses' => 'mgcp\CuadroCosto\GastosController@index'
                ));
                Route::get('nuevo', array(
                    'as' => 'nuevo',
                    'uses' => 'mgcp\CuadroCosto\GastosController@nuevo'
                ));
                Route::post('registrar', array(
                    'as' => 'registrar',
                    'uses' => 'mgcp\CuadroCosto\GastosController@registrar'
                ));
                Route::post('eliminar', array(
                    'as' => 'eliminar',
                    'uses' => 'mgcp\CuadroCosto\GastosController@eliminar'
                ));
                Route::post('activar', array(
                    'as' => 'activar',
                    'uses' => 'mgcp\CuadroCosto\GastosController@activar'
                ));
            });*/
        });

        Route::name('responsables.')->prefix('responsables')->group(function () { //Route::group(['as' => 'responsables.', 'prefix' => 'responsables'], function () {
            Route::post('agregar', [ResponsableController::class, 'agregar'])->name('agregar');
            Route::post('actualizar', [ResponsableController::class, 'actualizar'])->name('actualizar');
            Route::post('eliminar', [ResponsableController::class, 'eliminar'])->name('eliminar');
        });

        Route::name('solicitudes.')->prefix('solicitudes')->group(function () { //Route::group(['as' => 'solicitudes.', 'prefix' => 'solicitudes'], function () {
            Route::post('listar', [SolicitudController::class, 'listar'])->name('listar');
            Route::post('nueva', [SolicitudController::class, 'nueva'])->name('nueva');
            Route::post('responder', [SolicitudController::class, 'responder'])->name('responder');
            Route::post('consulta-solicitud-previa', [SolicitudController::class, 'consultaSolicitudPrevia'])->name('consulta-solicitud-previa');
            Route::post('solicitud-previa', [SolicitudController::class, 'solicitudPrevia'])->name('solicitud-previa');
        });

        Route::name('ccam.')->prefix('ccam')->group(function () {
            Route::post('actualizar-campo-fila', [CcAmController::class, 'actualizarCampoFila'])->name('actualizar-campo-fila');
            Route::post('actualizar-campo', [CcAmController::class, 'actualizarCampo'])->name('actualizar-campo');
            Route::post('obtener-detalles-fila', [CcAmController::class, 'obtenerDetallesFila'])->name('obtener-detalles-fila');
            Route::post('obtener-historial-precios', [CcAmController::class, 'obtenerHistorialPrecios'])->name('obtener-historial-precios');
            Route::post('actualizar-campo-proveedor', [CcAmController::class, 'actualizarCampoProveedor'])->name('actualizar-campo-proveedor');
            Route::post('actualizar-compra-fila', [CcAmController::class, 'actualizarCompraFila'])->name('actualizar-compra-fila');
            Route::post('agregar-fila', [CcAmController::class, 'agregarFila'])->name('agregar-fila');
            Route::post('listar-comentarios', [CcAmController::class, 'listarComentarios'])->name('listar-comentarios');
            Route::post('registrar-comentario', [CcAmController::class, 'registrarComentario'])->name('registrar-comentario');
            Route::post('buscar-nro-parte', [CcAmController::class, 'buscarNroParte'])->name('buscar-nro-parte');
            Route::post('obtener-proveedores-fila', [CcAmController::class, 'obtenerProveedoresFila'])->name('obtener-proveedores-fila');
            Route::post('seleccionar-proveedor-fila', [CcAmController::class, 'seleccionarProveedorFila'])->name('seleccionar-proveedor-fila');
            Route::post('eliminar-proveedor-fila', [CcAmController::class, 'eliminarProveedorFila'])->name('eliminar-proveedor-fila');
            Route::post('seleccionar-mejor-precio', [CcAmController::class, 'seleccionarMejorPrecio'])->name('seleccionar-mejor-precio');
            Route::post('agregar-proveedor-fila', [CcAmController::class, 'agregarProveedorFila'])->name('agregar-proveedor-fila');
            Route::post('eliminar-fila', [CcAmController::class, 'eliminarFila'])->name('eliminar-fila');
            Route::post('obtener-licencias', [CcAmController::class, 'obtenerLicencias'])->name('obtener-licencias');
            Route::post('obtener-fondos-ms', [CcAmController::class, 'obtenerFondosMS'])->name('obtener-fondos-ms');
            Route::name('transformacion.')->prefix('transformacion')->group(function () {
                Route::post('obtener-detalles', [MovimientoTransformacionController::class, 'obtenerDetalles'])->name('obtener-detalles');
                Route::post('actualizar-fila', [MovimientoTransformacionController::class, 'actualizarFila'])->name('actualizar-fila');
                Route::post('eliminar-fila', [MovimientoTransformacionController::class, 'eliminarFila'])->name('eliminar-fila');
                Route::post('agregar-fila', [MovimientoTransformacionController::class, 'agregarFila'])->name('agregar-fila');
                //Route::post('obtener-filas-cuadro', [MovimientoTransformacionController::class, 'obtenerFilasCuadro'])->name('obtener-filas-cuadro');
            });
        });

        Route::name('ccbs.')->prefix('ccbs')->group(function () { //Route::group(['as' => 'ccbs.', 'prefix' => 'ccbs'], function () {
            Route::post('obtener-historial-precios', [CcBsController::class, 'obtenerHistorialPrecios'])->name('obtener-historial-precios');
            Route::post('actualizar-campo-proveedor', [CcBsController::class, 'actualizarCampoProveedor'])->name('actualizar-campo-proveedor');
            Route::post('actualizar-campo', [CcBsController::class, 'actualizarCampo'])->name('actualizar-campo');
            Route::post('actualizar-campo-fila', [CcBsController::class, 'actualizarCampoFila'])->name('actualizar-campo-fila');
            Route::post('buscar-nro-parte', [CcBsController::class, 'buscarNroParte'])->name('buscar-nro-parte');
            Route::post('actualizar-compra-fila', [CcBsController::class, 'actualizarCompraFila'])->name('actualizar-compra-fila');
            Route::post('obtener-proveedores-fila', [CcBsController::class, 'obtenerProveedoresFila'])->name('obtener-proveedores-fila');
            Route::post('seleccionar-mejor-precio', [CcBsController::class, 'seleccionarMejorPrecio'])->name('seleccionar-mejor-precio');
            Route::post('seleccionar-proveedor-fila', [CcBsController::class, 'seleccionarProveedorFila'])->name('seleccionar-proveedor-fila');
            Route::post('eliminar-proveedor-fila', [CcBsController::class, 'eliminarProveedorFila'])->name('eliminar-proveedor-fila');
            Route::post('agregar-proveedor-fila', [CcBsController::class, 'agregarProveedorFila'])->name('agregar-proveedor-fila');
            Route::post('agregar-fila', [CcBsController::class, 'agregarFila'])->name('agregar-fila');
            Route::post('eliminar-fila', [CcBsController::class, 'eliminarFila'])->name('eliminar-fila');
        });

        Route::name('ccgg.')->prefix('ccgg')->group(function () { //Route::group(['as' => 'ccgg.', 'prefix' => 'ccgg', 'middleware' => 'auth'], function () {
            Route::post('actualizar-visible', [CcGgController::class, 'actualizarVisible'])->name('actualizar-visible');
            Route::post('eliminar-fila', [CcGgController::class, 'eliminarFila'])->name('eliminar-fila');
            Route::post('agregar-fila', [CcGgController::class, 'agregarFila'])->name('agregar-fila');
            Route::post('actualizar-campo-fila', [CcGgController::class, 'actualizarCampoFila'])->name('actualizar-campo-fila');
        });
    });

    Route::name('acuerdo-marco.')->prefix('acuerdo-marco')->middleware('auth')->group(function () { //Route::group(['as' => 'acuerdo-marco.', 'prefix' => 'acuerdo-marco'], function () {
        Route::name('notificaciones.')->prefix('notificaciones')->group(function () { //Route::group(['as' => 'notificaciones.', 'prefix' => 'notificaciones'], function () {
            Route::get('lista', [App\Http\Controllers\mgcp\AcuerdoMarco\NotificacionController::class, 'lista'])->name('lista');
            Route::post('data-lista', [App\Http\Controllers\mgcp\AcuerdoMarco\NotificacionController::class, 'dataLista'])->name('data-lista');
            Route::get('actualizar-lista/{tipo}', [App\Http\Controllers\mgcp\AcuerdoMarco\NotificacionController::class, 'actualizarLista'])->name('actualizar-lista');
            Route::post('obtener-fechas-descarga', [App\Http\Controllers\mgcp\AcuerdoMarco\NotificacionController::class, 'obtenerFechasDescarga'])->name('obtener-fechas-descarga');
            Route::post('obtener-detalles-notificacion', [App\Http\Controllers\mgcp\AcuerdoMarco\NotificacionController::class, 'obtenerDetallesNotificacion'])->name('obtener-detalles-notificacion');
            Route::post('obtener-historial-notificacion', [App\Http\Controllers\mgcp\AcuerdoMarco\NotificacionController::class, 'obtenerHistorialNotificacion'])->name('obtener-historial-notificacion');
        });

        Route::name('empresas.')->prefix('empresas')->group(function () { //Route::group(['as' => 'empresas.', 'prefix' => 'empresas'], function () {
            Route::get('cambiar-claves', [EmpresaController::class, 'cambiarClaves'])->name('cambiar-claves');
            Route::get('actualizar-claves', [EmpresaController::class, 'actualizarClaves'])->name('actualizar-claves');
        });

        Route::name('transportistas.')->prefix('transportistas')->group(function () { //Route::group(['as' => 'empresas.', 'prefix' => 'empresas'], function () {
            Route::post('lista', [TransportistaController::class, 'lista'])->name('lista');
            Route::post('registrar', [TransportistaController::class, 'registrar'])->name('registrar');
        });

        Route::name('entidades.')->prefix('entidades')->group(function () { //Route::group(['as' => 'empresas.', 'prefix' => 'empresas'], function () {
            Route::post('buscar-ruc', [EntidadController::class, 'buscarRuc'])->name('buscar-ruc');
            Route::post('buscar-nombre', [EntidadController::class, 'buscarNombre'])->name('buscar-nombre');
            Route::post('detalles', [EntidadController::class, 'detalles'])->name('detalles');
            Route::post('actualizar-campo', [EntidadController::class, 'actualizarCampo'])->name('actualizar-campo');
            Route::post('registrar', [EntidadController::class, 'registrar'])->name('registrar');
            Route::post('buscar-entidad', [EntidadController::class, 'buscarEntidad'])->name('buscar-entidad');
            Route::get('lista', [EntidadController::class, 'lista'])->name('lista');
            Route::post('data-lista', [EntidadController::class, 'dataLista'])->name('data-lista');
            Route::post('actualizar', [EntidadController::class, 'actualizar'])->name('actualizar');

            Route::name('contactos.')->prefix('contactos')->group(function () {
                Route::post('listar', [ContactoEntidadController::class, 'listar'])->name('listar');
                Route::post('eliminar', [ContactoEntidadController::class, 'eliminar'])->name('eliminar');
                Route::post('agregar', [ContactoEntidadController::class, 'agregar'])->name('agregar');
                Route::post('actualizar', [ContactoEntidadController::class, 'actualizar'])->name('actualizar');
                Route::post('obtener-detalles', [ContactoEntidadController::class, 'obtenerDetalles'])->name('obtener-detalles');
            });
        });

        Route::name('peru-compras.')->prefix('peru-compras')->group(function () { //Route::group(['as' => 'peru-compras.', 'prefix' => 'peru-compras'], function () {
            Route::post('obtener-acuerdos', [PeruComprasController::class, 'obtenerAcuerdos'])->name('obtener-acuerdos');
            Route::post('obtener-acuerdos-local', [PeruComprasController::class, 'obtenerAcuerdosLocal'])->name('obtener-acuerdos-local');
            Route::post('obtener-catalogos', [PeruComprasController::class, 'obtenerCatalogos'])->name('obtener-catalogos');
            Route::post('obtener-categorias', [PeruComprasController::class, 'obtenerCategorias'])->name('obtener-categorias');
            Route::post('obtener-departamentos', [PeruComprasController::class, 'obtenerDepartamentos'])->name('obtener-departamentos');
            Route::post('obtener-provincias', [PeruComprasController::class, 'obtenerProvincias'])->name('obtener-provincias');
        });

        Route::name('proformas.')->prefix('proformas')->group(function () { //Route::group(['as' => 'proformas.', 'prefix' => 'proformas', 'middleware' => 'auth'], function () {
            Route::name('exportar.')->prefix('exportar')->group(function () {
                Route::get('index', [App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ExportarController::class, 'index'])->name('index');
                Route::get('entidades', [App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ExportarController::class, 'entidades'])->name('entidades');
                Route::post('generar-archivo', [App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ExportarController::class, 'generarArchivo'])->name('generar-archivo');
                Route::post('generar-entidades', [App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ExportarController::class, 'generarEntidades'])->name('generar-entidades');
            });

            Route::name('calculadora-producto.')->prefix('calculadora-producto')->group(function () {
                //Route::get('index', [App\Http\Controllers\mgcp\AcuerdoMarco\Proforma\ExportarController::class, 'index'])->name('index');
                Route::post('listar', [CalculadoraProductoController::class, 'listar'])->name('listar');
                Route::post('agregar-fila', [CalculadoraProductoController::class, 'agregarFila'])->name('agregar-fila');
                Route::post('eliminar-fila', [CalculadoraProductoController::class, 'eliminarFila'])->name('eliminar-fila');
                Route::post('actualizar-campo', [CalculadoraProductoController::class, 'actualizarCampo'])->name('actualizar-campo');
                Route::post('aplicar-precios-proformas', [CalculadoraProductoController::class, 'aplicarPreciosProformas'])->name('aplicar-precios-proformas');
            });

            Route::name('individual.')->prefix('individual')->group(function () {
                Route::post('obtener-lista-para-enviar-portal', [ProformaIndividualController::class, 'obtenerListaParaEnviarPortal'])->name('obtener-lista-para-enviar-portal');
                Route::post('enviar-cotizacion-portal', [ProformaIndividualController::class, 'enviarCotizacionPortal'])->name('enviar-cotizacion-portal');
                Route::post('actualizar-campo', [ProformaIndividualController::class, 'actualizarCampo'])->name('actualizar-campo');
                Route::post('deshacer-cotizacion', [ProformaIndividualController::class, 'deshacerCotizacion'])->name('deshacer-cotizacion');
                Route::post('obtener-detalles', [ProformaIndividualController::class, 'obtenerDetalles'])->name('obtener-detalles');
                Route::post('actualizar-restringir', [ProformaIndividualController::class, 'actualizarRestringir'])->name('actualizar-restringir');
                Route::post('actualizar-probabilidad', [ProformaAnalisisController::class, 'actualizarProbabilidad'])->name('actualizar-probabilidad');
                Route::post('filtrar-analisis', [ProformaAnalisisController::class, 'actualizarFiltros'])->name('filtrar-analisis');
                Route::get('descargar-analisis/{tipo}', [ProformaAnalisisController::class, 'descargarFormato'])->name('descargar-analisis');

                Route::name('compra-ordinaria.')->prefix('compra-ordinaria')->group(function () {
                    Route::post('obtener-comentarios', [ProformaIndividualController::class, 'obtenerComentarios'])->name('obtener-comentarios');
                    Route::post('registrar-comentario', [ProformaIndividualController::class, 'registrarComentario'])->name('registrar-comentario');
                    Route::post('busqueda-producto', [ProductoController::class, 'busquedaProductoPN'])->name('busqueda-producto');

                    Route::name('vista-anterior.')->prefix('vista-anterior')->group(function () {
                        Route::get('index', [COIVistaAnteriorController::class, 'index'])->name('index');
                        Route::post('generar-lista-para-datatable', [COIVistaAnteriorController::class, 'generarListaParaDatatable'])->name('generar-lista-para-datatable');
                        Route::post('ingresar-flete-por-lote', [COIVistaAnteriorController::class, 'ingresarFletePorLote'])->name('ingresar-flete-por-lote');
                    });
                    Route::name('nueva-vista.')->prefix('nueva-vista')->group(function () {
                        Route::get('index', [COINuevaVistaController::class, 'index'])->name('index');
                        Route::post('obtener-proformas', [COINuevaVistaController::class, 'obtenerProformas'])->name('obtener-proformas');
                        Route::post('ingresar-flete-por-lote', [COINuevaVistaController::class, 'ingresarFletePorLote'])->name('ingresar-flete-por-lote');
                        Route::post('listar', [ProformaAnalisisController::class, 'listar'])->name('listar');
                        Route::post('registrar', [ProformaAnalisisController::class, 'registrar'])->name('registrar');
                    });
                });

                Route::name('gran-compra.')->prefix('gran-compra')->group(function () { //Route::group(['as' => 'gran-compra.', 'prefix' => 'gran-compra'], function () {
                    Route::name('vista-anterior.')->prefix('vista-anterior')->group(function () {
                        Route::get('index', [GCIVistaAnteriorController::class, 'index'])->name('index');
                        Route::post('generar-lista-para-datatable', [GCIVistaAnteriorController::class, 'generarListaParaDatatable'])->name('generar-lista-para-datatable');
                    });
                    Route::name('nueva-vista.')->prefix('nueva-vista')->group(function () {
                        Route::get('index', [GCINuevaVistaController::class, 'index'])->name('index');
                        Route::post('obtener-proformas', [GCINuevaVistaController::class, 'obtenerProformas'])->name('obtener-proformas');
                    });
                });
            });

            Route::name('paquete.')->prefix('paquete')->group(function () { //Route::group(['as' => 'paquete.', 'prefix' => 'paquete'], function () {
                //Route::post('cantidad-pendientes', [ProformaPaqueteController::class, 'cantidadPendientes'])->name('cantidad-pendientes');
                //Route::get('lista-pendientes', [ProformaPaqueteController::class, 'listaPendientes'])->name('lista-pendientes');
                Route::post('obtener-lista-para-enviar-portal', [ProformaPaqueteController::class, 'obtenerListaParaEnviarPortal'])->name('obtener-lista-para-enviar-portal');
                Route::post('enviar-cotizacion-portal', [ProformaPaqueteController::class, 'enviarCotizacionPortal'])->name('enviar-cotizacion-portal');
                Route::post('actualizar-campo', [ProformaPaqueteController::class, 'actualizarCampo'])->name('actualizar-campo');
                Route::post('deshacer-cotizacion', [ProformaPaqueteController::class, 'deshacerCotizacion'])->name('deshacer-cotizacion');
                Route::post('obtener-proformas', [ProformaPaqueteController::class, 'obtenerProformas'])->name('obtener-proformas');
                Route::post('actualizar-seleccion', [ProformaPaqueteController::class, 'actualizarSeleccion'])->name('actualizar-seleccion');
                Route::post('actualizar-precio', [ProformaPaqueteController::class, 'actualizarPrecio'])->name('actualizar-precio');
                Route::post('actualizar-costo-envio', [ProformaPaqueteController::class, 'actualizarCostoEnvio'])->name('actualizar-costo-envio');

                Route::name('compra-ordinaria.')->prefix('compra-ordinaria')->group(function () {
                    Route::get('index', [CompraOrdinariaPaqueteController::class, 'index'])->name('index');

                });
                Route::name('gran-compra.')->prefix('gran-compra')->group(function () {
                    Route::get('index', [GranCompraPaqueteController::class, 'index'])->name('index');
                });
            });
        });

        Route::name('productos.')->prefix('productos')->group(function () { //Route::group(['as' => 'productos.', 'prefix' => 'productos'], function () {
            Route::name('equipos-computo.')->prefix('equipos-computo')->group(function () {
                Route::get('busqueda', [EquipoComputoController::class, 'busqueda'])->name('busqueda');
            });
            Route::get('lista', [ProductoController::class, 'lista'])->name('lista');
            Route::post('data-lista', [ProductoController::class, 'dataLista'])->name('data-lista');
            Route::post('actualizar-filtros', [ProductoController::class, 'actualizarFiltros'])->name('actualizar-filtros');
            Route::post('obtener-precio-stock-portal', [ProductoController::class, 'obtenerPrecioStockPortal'])->name('obtener-precio-stock-portal');
            Route::post('actualizar-precio-stock-portal', [ProductoController::class, 'actualizarPrecioStockPortal'])->name('actualizar-precio-stock-portal');
            Route::post('obtener-detalles-por-id', [ProductoController::class, 'obtenerDetallesPorId'])->name('obtener-detalles-por-id');
            Route::post('obtener-detalles-por-mmn', [ProductoController::class, 'obtenerDetallesPorMMN'])->name('obtener-detalles-por-mmn');
            Route::post('actualizar-estado-stock', [ProductoController::class, 'actualizarEstadoStock'])->name('actualizar-estado-stock');

            Route::name('catalogos.')->prefix('catalogos')->group(function () { //Route::group(['as' => 'catalogos.', 'prefix' => 'catalogos'], function () {
                Route::post('listar-por-acuerdo', [CatalogoController::class, 'listarPorAcuerdo'])->name('listar-por-acuerdo');
            });
            Route::name('categorias.')->prefix('categorias')->group(function () { //Route::group(['as' => 'categorias.', 'prefix' => 'categorias'], function () {
                Route::post('listar-por-catalogo', [CategoriaController::class, 'listarPorCatalogo'])->name('listar-por-catalogo');
            });

            /*Route::group(['as' => 'descuentos-volumen.', 'prefix' => 'descuentos-volumen'], function () {
                Route::post('obtener-descuentos-producto', array(
                    'as' => 'obtener-descuentos-producto',
                    'uses' => 'mgcp\AcuerdoMarco\Producto\DescuentoVolumenController@obtenerDescuentosProducto'
                ));
            });*/
            Route::name('historial-actualizaciones.')->prefix('historial-actualizaciones')->group(function () { //Route::group(['as' => 'historial-actualizaciones.', 'prefix' => 'historial-actualizaciones'], function () {
                Route::get('lista', [HistorialActualizacionController::class, 'lista'])->name('lista');
                Route::post('data-lista', [HistorialActualizacionController::class, 'dataLista'])->name('data-lista');
                Route::post('obtener-historial-producto', [HistorialActualizacionController::class, 'obtenerHistorialProducto'])->name('obtener-historial-producto');
            });
        });

        Route::name('descargar.')->prefix('descargar')->group(function () { //Route::group(['as' => 'descargar.', 'prefix' => 'descargar'], function () {
            Route::name('ordenes-compra-publicas.')->prefix('ordenes-compra-publicas')->group(function () { //Route::group(['as' => 'ordenes-compra-publicas.', 'prefix' => 'ordenes-compra-publicas'], function () {
                Route::get('index', [App\Http\Controllers\mgcp\AcuerdoMarco\Descarga\OrdenCompraPublicaController::class, 'index'])->name('index');
                Route::post('obtener-detalles', [App\Http\Controllers\mgcp\AcuerdoMarco\Descarga\OrdenCompraPublicaController::class, 'obtenerDetalles'])->name('obtener-detalles');
                Route::post('procesar', [App\Http\Controllers\mgcp\AcuerdoMarco\Descarga\OrdenCompraPublicaController::class, 'procesar'])->name('procesar');
            });

            Route::name('proformas.')->prefix('proformas')->group(function () { //Route::group(['as' => 'proformas.', 'prefix' => 'proformas'], function () {
                Route::get('index', [ProformaController::class, 'index'])->name('index');
                Route::post('lista-ultimas-descargas', [ProformaController::class, 'listaUltimasDescargas'])->name('lista-ultimas-descargas');
                Route::post('obtener-fechas-ultima-descarga', [ProformaController::class, 'obtenerFechasUltimaDescarga'])->name('obtener-fechas-ultima-descarga');
                Route::post('registrar-descarga', [ProformaController::class, 'registrarDescarga'])->name('registrar-descarga');
                Route::post('obtener-proformas', [ProformaController::class, 'obtenerProformas'])->name('obtener-proformas');
                Route::post('procesar-proforma', [ProformaController::class, 'procesarProforma'])->name('procesar-proforma');
                Route::post('procesar-proforma-paquete', [ProformaController::class, 'procesarProformaPaquete'])->name('procesar-proforma-paquete');
            });

            Route::name('productos-adjudicados.')->prefix('productos-adjudicados')->group(function () { //Route::group(['as' => 'productos-adjudicados.', 'prefix' => 'productos-adjudicados'], function () {
                Route::get('index', [ProductoAdjudicadoController::class, 'index'])->name('index');
                Route::post('obtener-productos', [ProductoAdjudicadoController::class, 'obtenerProductos'])->name('obtener-productos');
                Route::post('procesar', [ProductoAdjudicadoController::class, 'procesar'])->name('procesar');
            });

            Route::name('nuevos-productos.')->prefix('nuevos-productos')->group(function () { //Route::group(['as' => 'nuevos-productos.', 'prefix' => 'nuevos-productos'], function () {
                Route::get('index', [NuevoProductoController::class, 'index'])->name('index');
                Route::post('obtener-productos', [NuevoProductoController::class, 'obtenerProductos'])->name('obtener-productos');
                Route::post('procesar', [NuevoProductoController::class, 'procesar'])->name('procesar');
            });

            /*Route::name('nuevos-productos.')->prefix('nuevos-productos')->group(function () { //Route::group(['as' => 'plazos-entrega.', 'prefix' => 'plazos-entrega'], function () {
                Route::get('index', array(
                    'as' => 'index',
                    'uses' => 'mgcp\AcuerdoMarco\Descarga\PlazoEntregaController@index'
                ));

                Route::post('procesar', array(
                    'as' => 'procesar',
                    'uses' => 'mgcp\AcuerdoMarco\Descarga\PlazoEntregaController@procesar'
                ));
            });*/
        });

        Route::name('publicar.')->prefix('publicar')->group(function () { //Route::group(['as' => 'publicar.', 'prefix' => 'publicar'], function () {
            Route::name('stock-productos.')->prefix('stock-productos')->group(function () { //Route::group(['as' => 'stock-productos.', 'prefix' => 'stock-productos'], function () {
                Route::get('index', [StockProductoController::class, 'index'])->name('index');
                Route::post('obtener-productos', [StockProductoController::class, 'obtenerProductos'])->name('obtener-productos');
                Route::post('procesar', [StockProductoController::class, 'procesar'])->name('procesar');
            });
            Route::name('stock-empresa.')->prefix('stock-empresa')->group(function () { //Route::group(['as' => 'stock-productos.', 'prefix' => 'stock-productos'], function () {
                Route::get('index', [StockEmpresaController::class, 'index'])->name('index');
                Route::post('obtener-productos', [StockEmpresaController::class, 'obtenerProductos'])->name('obtener-productos');
                Route::post('enviar-portal', [StockEmpresaController::class, 'enviarPortal'])->name('enviar-portal');
                Route::post('procesar-archivo', [StockEmpresaController::class, 'procesarArchivo'])->name('procesar-archivo');
                Route::get('descargar-plantilla', [StockEmpresaController::class, 'descargarPlantilla'])->name('descargar-plantilla');
            });
            Route::name('plazos-entrega.')->prefix('plazos-entrega')->group(function () { //Route::group(['as' => 'plazos-entrega.', 'prefix' => 'plazos-entrega'], function () {
                Route::get('index', [PlazoEntregaController::class, 'index'])->name('index');
                Route::post('procesar', [PlazoEntregaController::class, 'procesar'])->name('procesar');
                Route::post('obtener-categorias-por-acuerdo', [PlazoEntregaController::class, 'obtenerCategoriasPorAcuerdo'])->name('obtener-categorias-por-acuerdo');
                Route::post('obtener-productos-por-categoria', [PlazoEntregaController::class, 'obtenerProductosporCategoria'])->name('obtener-productos-por-categoria');
            });
            Route::name('nuevos-productos.')->prefix('nuevos-productos')->group(function () { //Route::group(['as' => 'nuevos-productos.', 'prefix' => 'nuevos-productos'], function () {
                Route::get('index', [App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\NuevoProductoController::class, 'index'])->name('index');
                Route::post('obtener-productos', [App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\NuevoProductoController::class, 'obtenerProductos'])->name('obtener-productos');
                Route::post('procesar', [App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\NuevoProductoController::class, 'procesar'])->name('procesar');
                Route::post('procesar-archivo', [App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\NuevoProductoController::class, 'procesarArchivo'])->name('procesar-archivo');
                Route::get('confirmar-portal/{idEmpresa}/{tipo}', [App\Http\Controllers\mgcp\AcuerdoMarco\Publicar\NuevoProductoController::class, 'confirmarPortal'])->name('confirmar-portal');
            });
            Route::name('nuevos-precios.')->prefix('nuevos-precios')->group(function () { //Route::group(['as' => 'nuevos-precios.', 'prefix' => 'nuevos-precios'], function () {
                Route::get('index', [NuevoPrecioController::class, 'index'])->name('index');
                Route::post('obtener-productos', [NuevoPrecioController::class, 'obtenerProductos'])->name('obtener-productos');
                Route::post('procesar', [NuevoPrecioController::class, 'procesar'])->name('procesar');
                Route::post('procesar-archivo', [NuevoPrecioController::class, 'procesarArchivo'])->name('procesar-archivo');
            });
        });
    });

    Route::name('oportunidades.')->prefix('oportunidades')->middleware('auth')->group(function () { //Route::group(['as' => 'oportunidades.', 'prefix' => 'oportunidades', 'middleware' => 'auth'], function () {
        Route::get('nueva', [OportunidadController::class, 'nueva'])->name('nueva');
        Route::get('lista', [OportunidadController::class, 'lista'])->name('lista');
        Route::post('data-lista', [OportunidadController::class, 'dataLista'])->name('data-lista');
        Route::post('data-lista-para-oc', [OportunidadController::class, 'dataListaParaOc'])->name('data-lista-para-oc');
        Route::post('obtener-detalles', [OportunidadController::class, 'obtenerDetalles'])->name('obtener-detalles');
        Route::get('detalles/{oportunidad?}', [OportunidadController::class, 'detalles'])->name('detalles');
        Route::get('descargar/{tipo}/{id}/{archivo}', [OportunidadController::class, 'descargar'])->name('descargar');
        Route::get('resumen', [OportunidadController::class, 'resumen'])->name('resumen');
        Route::post('resumen-detalles-monto', [OportunidadController::class, 'resumenDetallesMonto'])->name('resumen-detalles-monto');
        Route::post('resumen-data', [OportunidadController::class, 'resumenData'])->name('resumen-data');
        Route::post('actualizar-filtros', [OportunidadController::class, 'actualizarFiltros'])->name('actualizar-filtros');
        Route::post('registrar', [OportunidadController::class, 'registrar'])->name('registrar');
        Route::post('actualizar', [OportunidadController::class, 'actualizar'])->name('actualizar');
        Route::post('eliminar', [OportunidadController::class, 'eliminar'])->name('eliminar');
        Route::get('imprimir/{id}', [OportunidadController::class, 'imprimir'])->name('imprimir');
        Route::post('obtener-archivos', [OportunidadController::class, 'obtenerArchivos'])->name('obtener-archivos');
        Route::post('retirar-notificacion', [OportunidadController::class, 'retirarNotificacion'])->name('retirar-notificacion');
        Route::post('agregar-notificacion', [OportunidadController::class, 'agregarNotificacion'])->name('agregar-notificacion');
        Route::post('enviar-correo', [OportunidadController::class, 'enviarCorreo'])->name('enviar-correo');
        Route::post('ingresar-status', [OportunidadController::class, 'ingresarStatus'])->name('ingresar-status');
        Route::post('ingresar-actividad', [OportunidadController::class, 'ingresarActividad'])->name('ingresar-actividad');
        Route::post('ingresar-comentario', [OportunidadController::class, 'ingresarComentario'])->name('ingresar-comentario');
        Route::post('crear-desde-oc-propia', [OportunidadController::class, 'crearDesdeOcPropia'])->name('crear-desde-oc-propia');
    });

	Route::name('indicadores.')->prefix('indicadores')->middleware('auth')->group(function () {
        Route::name('dashboard.')->prefix('dashboard')->middleware('auth')->group(function () { //Route::group(['as' => 'perfil.', 'prefix' => 'perfil', 'middleware' => 'auth'], function () {
            Route::get('index', [DashboardController::class, 'index'])->name('index');
            Route::post('obtener-indicadores-cdp-por-periodo', [DashboardController::class, 'obtenerIndicadoresCdpPorPeriodo'])->name('obtener-indicadores-cdp-por-periodo');
            Route::post('obtener-montos-adjudicados-ordenes-por-anio', [DashboardController::class, 'obtenerMontosAdjudicadosOrdenesPorAnio'])->name('obtener-montos-adjudicados-ordenes-por-anio');
            Route::post('obtener-montos-facturados-terceros-por-anio', [DashboardController::class, 'obtenerMontosFacturadosTercerosPorAnio'])->name('obtener-montos-facturados-terceros-por-anio');
        });

        Route::name('meta.')->prefix('meta')->group(function () {
            Route::get('meta-empresa-mensual', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'nuevaEmpresaMensual'])->name('meta-empresa-mensual');
            Route::get('meta-division-mensual', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'nuevaDivisionMensual'])->name('meta-division-mensual');
            Route::get('meta-corporativo-mensual', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'nuevaCorporativoMensual'])->name('meta-corporativo-mensual');
            Route::get('dashboard', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'viewDashboardContabilidad'])->name('dashboard');
        });
        Route::get('ventas-empresa', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'ventaEmpresa'])->name('ventas-empresa');
        Route::get('ventas-division', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'ventaDivision'])->name('ventas-division');

        Route::post('busqueda', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'busqueda'])->name('busqueda');
        Route::post('busqueda-dashboard', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'busquedaDashboard'])->name('busqueda-dashboard');
        Route::post('guardar-mensual', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'guardarMensual'])->name('guardar-mensual');
        Route::post('guardar-division', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'guardarDivision'])->name('guardar-division');
        Route::post('guardar-vendedor', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'guardarVendedor'])->name('guardar-vendedor');

        Route::post('mostrar-metas-mensuales', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'mostrarMensual'])->name('mostrar-metas-mensuales');
        Route::post('mostrar-metas-division', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'mostrarDivision'])->name('mostrar-metas-division');
        Route::post('buscar-meta-division', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'mostrarDivisionId'])->name('buscar-meta-division');
        Route::post('listar-divisiones', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'listaDivision'])->name('listar-divisiones');
        Route::post('listar-vendedores', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'listaVendedor'])->name('listar-vendedores');

        Route::get('test-kpi/{empresa}/{inicio}/{fin}/{venta}/{tipo}', [App\Http\Controllers\mgcp\Indicadores\IndicadorController::class, 'importeVentasEmpresa'])->name('test-kpi');
    });

    Route::name('integraciones.')->prefix('integraciones')->middleware('auth')->group(function () {
        Route::name('ceam.')->prefix('ceam')->group(function () {
            Route::name('productos.')->prefix('productos')->group(function () {
                Route::get('index', [ProductoCeam::class, 'index'])->name('index');
                Route::post('lista', [ProductoCeam::class, 'lista'])->name('lista');
                Route::get('descargar-plantilla', [ProductoCeam::class, 'descargarPlantilla'])->name('descargar-plantilla');
                Route::post('importar', [ProductoCeam::class, 'importar'])->name('importar');
            });
        });
    });
});
