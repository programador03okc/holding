<?php

namespace App\Http\Controllers\mgcp\OrdenCompra\Propia;

use App\Helpers\mgcp\ArchivoHelper;
use App\Helpers\mgcp\CuadroCosto\CuadroCostoHelper;
use App\Helpers\mgcp\OportunidadHelper;
use App\Helpers\mgcp\OrdenCompraDirectaHelper;
use App\Models\User;
use App\Models\mgcp\Usuario\Notificacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\CuadroCosto\TipoCambio;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Etapa;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\Usuario\LogActividad;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrdenCompraDirectaController extends Controller
{
    private $nombreFormulario = 'Órdenes de compra directas';

    public function nueva()
    {
        if (!Auth::user()->tieneRol(129)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        $tipoCambio = TipoCambio::first()->tipo_cambio;
        $responsables = User::whereRaw('activo=true AND id IN (SELECT id_usuario FROM mgcp_usuarios.roles_usuario WHERE (id_rol=8 OR id_rol=49))')->where('id_empresa', Auth::user()->id_empresa)->orderBy('name', 'asc')->get();
        $empresas = Empresa::where('activo', true)->orderBy('id', 'asc')->get();
        $etapas = Etapa::orderBy('id', 'asc')->get();
        return view('mgcp.orden-compra.propia.directa.nueva')->with(compact('empresas', 'responsables', 'etapas', 'tipoCambio'));
    }

    public function registrar(Request $request)
    {
        if (!Auth::user()->tieneRol(129)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $rules = array(
            'cliente' => 'required',
            'lugar_entrega' => 'required|max:255',
            'monto_total' => 'required',
            'moneda' => 'required',
            'responsable' => 'required',
            'fecha_entrega' => 'required',
            'fecha_publicacion' => 'required',
            'occ' => 'required|max:40',
            'archivos' => 'required|max:50',

        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("mgcp.ordenes-compra.propias.directas.nueva")->withErrors($validator)->withInput();
        } else {
            DB::beginTransaction();
            $registrado = false;
            try {
                $orden = new OrdenCompraDirecta();
                    $orden->nro_orden = OrdenCompraDirectaHelper::generarCodigo();
                    $orden->id_empresa = $request->id_empresa;
                    $orden->id_entidad = $request->cliente;
                    $orden->lugar_entrega = $request->lugar_entrega;
                    $orden->monto_total = $request->monto_total;
                    $orden->moneda = $request->moneda;
                    $orden->tipo_cambio = $request->moneda == 's' ? '1' : $request->tipo_cambio;
                    $orden->id_etapa = 1; //Etapa pendiente
                    if (Auth::user()->tieneRol(4)) {
                        $orden->id_corporativo = $request->responsable;
                    } else {
                        $orden->id_corporativo = Auth::user()->id;
                    }
                    $orden->cobrado = false; 
                    $orden->conformidad = false; 
                    $orden->eliminado = false;
                    $orden->fecha_entrega = $request->fecha_entrega;
                    $orden->fecha_publicacion = $request->fecha_publicacion;
                    $orden->occ = $request->occ;
                    $orden->despachada = false;
                $orden->save();
                if ($request->hasFile('archivos')) {
                    $archivos = $request->file('archivos');
                    foreach ($archivos as $archivo) {
                        $nombreFinal = ArchivoHelper::limpiarNombre($archivo->getClientOriginalName());
                        Storage::putFileAs('mgcp/ordenes-compra/directas/' . $orden->id, $archivo, $nombreFinal);
                    }
                }
                if ($request->crearOportunidad == '1') {
                    $ocView = OrdenCompraPropiaView::where('tipo','directa')->where('id', $orden->id)->first();
                    $oportunidad = OportunidadHelper::crearDesdeOcPropia('Oportunidad generada para O/C '.$orden->nro_orden, $orden->id_corporativo, $ocView);
                    $orden->id_oportunidad = $oportunidad->id;
                    $orden->save();
                    CuadroCostoHelper::crearDesdeOportunidad($oportunidad);
                }
                $registrado = true;
                DB::commit();
                $mensaje = 'Se ha registrado la orden con código ' . $orden->nro_orden;

                if ($request->crearOportunidad == '1')  {
                    $url = route('mgcp.cuadro-costos.detalles', ['id' => $orden->id_oportunidad]);
                    $mensaje.= '. Para ver el cuadro de presupuesto, haga clic <a href="'.$url.'">aquí</a>';
                }
                $request->session()->flash('alert-success', $mensaje);
            } catch (Exception $ex) {
                DB::rollback();
                $request->session()->flash('alert-danger', 'Hubo un problema al registrar la O/C. Por favor intente de nuevo. Error: ' . $ex->getMessage());
            }
            if ($registrado) {
                return redirect()->route("mgcp.ordenes-compra.propias.directas.nueva");
            } else {
                return redirect()->route("mgcp.ordenes-compra.propias.directas.nueva")->withInput();
            }
        }
    }

    public function descargarArchivo($id, $archivo)
    {
        $orden = OrdenCompraDirecta::find($id);
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 10, null, null, null, 'O/C: ' . $orden->nro_orden . ', archivo: ' . $archivo);
        return Storage::download('mgcp/ordenes-compra/directas/' . $id . '/' . $archivo);
    }
}
