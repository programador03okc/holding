<?php

namespace App\Http\Controllers\mgcp;

use App\Helpers\mgcp\OportunidadHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\Oportunidad\Status;
use App\Models\mgcp\Oportunidad\Actividad;
use App\Models\mgcp\Oportunidad\Comentario;
use App\Models\mgcp\Oportunidad\Grupo;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\Oportunidad\Estado;
use App\Models\mgcp\Oportunidad\TipoNegocio;
use App\Models\mgcp\Oportunidad\StatusArchivo;
use App\Models\mgcp\Oportunidad\ActividadArchivo;
use App\Models\mgcp\Oportunidad\Notificar;
use App\Models\mgcp\AcuerdoMarco\OrdenCompra\Propia\OrdenCompraPropia;
use App\Models\User;
use App\Helpers\mgcp\OportunidadNotificarHelper;
use App\Helpers\mgcp\OportunidadResumenHelper;
use App\Helpers\mgcp\ArchivoHelper;
use App\Helpers\mgcp\OrdenCompraPublicaHelper;
use App\Helpers\mgcp\ProductoHelper;
use App\Models\mgcp\AcuerdoMarco\AcuerdoMarco;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\OrdenCompra\Propia\AcuerdoMarco\OrdenCompraAm;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class OportunidadController extends Controller
{
    private $nombreFormulario = 'Oportunidades';

    public function lista()
    {
        if (!Auth::user()->tieneRol(1)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), "Lista de oportunidades", 1);
        $grupos = Grupo::orderBy('grupo', 'asc')->get();
        $tiposNegocio = TipoNegocio::orderBy('tipo', 'asc')->get();
        $responsables = User::whereRaw('id IN (SELECT id_responsable FROM mgcp_oportunidades.oportunidades WHERE eliminado=false)')->get();
        $estados = Estado::get();
        $clientes = Entidad::orderBy('nombre', 'asc')->get();
        return view('mgcp.oportunidad.lista', get_defined_vars());
    }

    public function nueva(Request $request)
    {
        if (!Auth::user()->tieneRol(3)) {
            return view('mgcp.usuario.sin_permiso');
        }
        LogActividad::registrar(Auth::user(), "Nueva oportunidad", 1);
        $codigo = Oportunidad::crearCodigo();
        $grupos = Grupo::orderBy('grupo', 'asc')->get();
        $tiposNegocio = TipoNegocio::orderBy('tipo', 'asc')->get();
        $responsables = User::obtenerPorRol(8)->where('id_empresa', Auth::user()->id_empresa);
        $entidades = Entidad::orderBy('nombre', 'asc')->get();
        return view('mgcp.oportunidad.nueva', get_defined_vars());
    }

    public function registrar(Request $request)
    {
        if (!Auth::user()->tieneRol(3)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $rules = array(
            'cliente' => 'required',
            'oportunidad' => 'required',
            'probabilidad' => 'required',
            'fecha_limite' => 'required',
            'tipo_moneda' => 'required|max:1',
            'importe' => 'required',
            'margen' => 'required',
            'grupo' => 'required|integer',
            'tipo_negocio' => 'required|integer',
            'nombre_contacto' => 'max:100',
            'telefono_contacto' => 'max:45',
            'correo_contacto' => 'max:100',
            'cargo_contacto' => 'max:45',
            'reportado_por' => 'max:100',
            'status' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("mgcp.oportunidades.nueva")->withErrors($validator)->withInput();
        } else {
            DB::beginTransaction();
            $registrado = false;

            try {
                $oportunidad = new Oportunidad;
                    $oportunidad->codigo_oportunidad = Oportunidad::crearCodigo();
                    $oportunidad->id_entidad = $request->cliente;
                    $oportunidad->oportunidad = $request->oportunidad;
                    $oportunidad->probabilidad = $request->probabilidad;
                    $oportunidad->fecha_limite = $request->fecha_limite;
                    $oportunidad->moneda = $request->tipo_moneda;
                    $oportunidad->importe = str_replace(',', '', $request->importe);
                    $oportunidad->margen = $request->margen;
                    $oportunidad->eliminado = 0;
                    $oportunidad->id_grupo = $request->grupo;
                    $oportunidad->id_tipo_negocio = $request->tipo_negocio;
                    if (Auth::user()->tieneRol(4)) {
                        $oportunidad->id_responsable = $request->responsable;
                    } else {
                        $oportunidad->id_responsable = Auth::user()->id;
                    }
                    $oportunidad->nombre_contacto = $request->nombre_contacto;
                    $oportunidad->telefono_contacto = $request->telefono_contacto;
                    $oportunidad->correo_contacto = $request->correo_contacto;
                    $oportunidad->cargo_contacto = $request->cargo_contacto;
                    $oportunidad->reportado_por = $request->reportado_por;
                    $oportunidad->id_estado = '1';
                    $oportunidad->id_empresa = $request->id_empresa;
                $oportunidad->save();

                $detalle = new Status;
                    $detalle->detalle = $request->status;
                    $detalle->id_oportunidad = $oportunidad->id;
                    $detalle->id_estado = 1;
                    $detalle->id_usuario = Auth::user()->id;
                $detalle->save();

                DB::commit();
                $registrado = true;
                LogActividad::registrar(Auth::user(), $this->nombreFormulario, 4, $oportunidad->getTable(), null, $oportunidad);
                $request->session()->flash('alert-success', 'Se ha registrado la oportunidad con código ' . $oportunidad->codigo_oportunidad);
            } catch (\Exception $ex) {
                DB::rollback();
                $request->session()->flash('alert-danger', 'Hubo un problema al registrar la oportunidad. Por favor intente de nuevo');
            }

            if ($registrado) {
                return redirect()->route("mgcp.oportunidades.nueva");
            } else {
                return redirect()->route("mgcp.oportunidades.nueva")->withInput();
            }
        }
    }

    public function actualizarFiltros(Request $request)
    {
        if ($request->chkFechaLimite == 'on') {
            session(['oport_fecha_limite_desde' => $request->fechaLimiteDesde]);
            session(['oport_fecha_limite_hasta' => $request->fechaLimiteHasta]);
        } else {
            $request->session()->forget('oport_fecha_limite_desde');
            $request->session()->forget('oport_fecha_limite_hasta');
        }

        if ($request->chkFechaCreacion == 'on') {
            session(['oport_fecha_creacion_desde' => $request->fechaCreacionDesde]);
            session(['oport_fecha_creacion_hasta' => $request->fechaCreacionHasta]);
        } else {
            $request->session()->forget('oport_fecha_creacion_desde');
            $request->session()->forget('oport_fecha_creacion_hasta');
        }

        if ($request->chkResponsable == 'on') {
            session(['oport_responsable' => $request->selectResponsable]);
        } else {
            $request->session()->forget('oport_responsable');
        }

        if ($request->chkEstado == 'on') {
            session(['oport_estado' => $request->selectEstado]);
        } else {
            $request->session()->forget('oport_estado');
        }

        if ($request->chkTipoNegocio == 'on') {
            session(['oport_tipo_negocio' => $request->selectTipoNegocio]);
        } else {
            $request->session()->forget('oport_tipo_negocio');
        }

        if ($request->chkProbabilidad == 'on') {
            session(['oport_probabilidad' => $request->selectProbabilidad]);
        } else {
            $request->session()->forget('oport_probabilidad');
        }
        return response()->json(array('tipo' => 'success', 'mensaje' => 'Se han aplicado los filtros'), 200);
    }

    public function dataLista(Request $request)
    {
        $valores['eliminado'] = 0;

        if ($request->session()->has('oport_responsable')) {
            $valores['id_responsable'] = session('oport_responsable');
        }

        if (Auth::user()->tieneRol(45)) {
            $valores['id_responsable'] = Auth::user()->id;
        }

        if ($request->session()->has('oport_estado')) {
            $valores['oportunidades.id_estado'] = session('oport_estado');
        }
        if ($request->session()->has('oport_tipo_negocio')) {
            $valores['id_tipo_negocio'] = session('oport_tipo_negocio');
        }

        if ($request->session()->has('oport_probabilidad')) {
            $valores['probabilidad'] = session('oport_probabilidad');
        }

        $oportunidades = Oportunidad::join('mgcp_acuerdo_marco.entidades', 'id_entidad', '=', 'entidades.id')
            ->join('mgcp_usuarios.users', 'id_responsable', '=', 'users.id')
            ->join('mgcp_oportunidades.estados', 'estados.id', '=', 'oportunidades.id_estado')
            ->join('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
            ->join('mgcp_oportunidades.grupos', 'grupos.id', '=', 'oportunidades.id_grupo')
            ->select([
                'oportunidades.id', 'codigo_oportunidad', 'entidades.nombre AS nombre_entidad', 'oportunidad', 'probabilidad', 'moneda', 'importe',
                'oportunidades.created_at', 'fecha_limite', 'margen', 'id_responsable', 'users.name', 'id_estado', 'estado', 'grupo', 'tipos_negocio.tipo'
            ])->where($valores)->where(function($query) {
                $query->where('oportunidades.id_empresa', Auth::user()->id_empresa)->orWhereNull('oportunidades.id_empresa');
            });

        if ($request->session()->has('oport_fecha_creacion_desde')) {
            $oportunidades = $oportunidades->whereRaw(" (oportunidades.created_at BETWEEN ? AND ?)", [
                Carbon::createFromFormat('d-m-Y H:i:s', session('oport_fecha_creacion_desde') . ' 00:00:00')->toDateTimeString(),
                Carbon::createFromFormat('d-m-Y H:i:s', session('oport_fecha_creacion_hasta') . ' 23:59:59')->toDateTimeString()
            ]);
        }
        
        if ($request->session()->has('oport_fecha_limite_desde')) {
            $oportunidades = $oportunidades->whereRaw(" (fecha_limite BETWEEN ? AND ?)", [
                Carbon::createFromFormat('d-m-Y H:i:s', session('oport_fecha_limite_desde') . ' 00:00:00')->toDateTimeString(),
                Carbon::createFromFormat('d-m-Y H:i:s', session('oport_fecha_limite_hasta') . ' 23:59:59')->toDateTimeString()
            ]);
        }

        if (!is_null($request->search['value'])) {
            LogActividad::registrar(Auth::user(), "Lista de oportunidades", 7, null, null, null, 'Criterio: ' . $request->search['value']);
        }
        return datatables($oportunidades)->toJson();
    }

    public function dataListaParaOc(Request $request)
    {
        $valores['eliminado'] = 0;
        $orden = OrdenCompraPropiaView::where('id',$request->idOrden)->where('tipo',$request->tipo)->first();
        if (!Auth::user()->tieneRol(4)) {
            $valores['id_responsable'] = Auth::user()->id;
        }
        $oportunidades = Oportunidad::join('mgcp_acuerdo_marco.entidades', 'id_entidad', '=', 'entidades.id')
            ->join('mgcp_usuarios.users', 'id_responsable', '=', 'users.id')
            ->join('mgcp_oportunidades.estados', 'estados.id', '=', 'oportunidades.id_estado')
            ->join('mgcp_oportunidades.tipos_negocio', 'tipos_negocio.id', '=', 'oportunidades.id_tipo_negocio')
            ->join('mgcp_oportunidades.grupos', 'grupos.id', '=', 'oportunidades.id_grupo')
            ->select([
                'oportunidades.id', 'codigo_oportunidad', 'entidades.nombre AS nombre_entidad', 'oportunidad', 'probabilidad', 'moneda', 'importe',
                'oportunidades.created_at', 'fecha_limite', 'margen', 'id_responsable', 'users.name', 'estados.id AS id_estado', 'estado', 'grupo', 'tipos_negocio.tipo'
            ])->where('eliminado', false)->where('id_entidad', $orden->id_entidad)
            ->whereRaw('oportunidades.id NOT IN (SELECT id_oportunidad FROM mgcp_ordenes_compra.oc_propias_view WHERE id_oportunidad IS NOT NULL)');
        return datatables($oportunidades)->toJson();
    }

    public function detalles($id)
    {
        if (!Auth::user()->tieneRol(1)) {
            return view('mgcp.usuario.sin_permiso');
        }

        $oportunidad = Oportunidad::find($id);
        if ($oportunidad == null || $oportunidad->eliminado == true) {
            return redirect()->route('mgcp.oportunidades.lista');
        }
        if (Auth::user()->tieneRol(45) && $oportunidad->id_responsable != Auth::user()->id) {
            return view('mgcp.usuario.sin_permiso');
        }
        $estados = Estado::orderBy('id', 'asc')->get();
        $statusOportunidad = $oportunidad->status()->orderBy('created_at', 'asc')->get(); //Oportunidadstatus::where('oportunidad_id', $oportunidad_id)->orderBy('created_at', 'asc')->get(); //$oportunidad->negocios;
        $actividades = $oportunidad->actividades()->orderBy('fecha_inicio', 'asc')->get();
        $comentarios = $oportunidad->comentarios()->orderBy('created_at', 'asc')->get();
        $notificaciones = $oportunidad->notificar()->orderBy('correo', 'asc')->get();
        $grupos = Grupo::orderBy('grupo', 'asc')->get();
        $responsables = User::all(); //Usuariorol::join('users', 'usuarios_roles.id_usuario', '=', 'users.id')->where('id_rol', 8)->orderBy('name', 'asc')->get();
        $tiposNegocio = TipoNegocio::orderBy('tipo', 'asc')->get();
        $clientes = Entidad::orderBy('nombre', 'asc')->get();

        LogActividad::registrar(Auth::user(), 'Detalles de oportunidad', 9, null, null, null, 'Código: ' . $oportunidad->codigo_oportunidad);
        return view('mgcp.oportunidad.detalles', get_defined_vars());
    }

    public function obtenerDetalles(Request $request)
    {
        if (!Auth::user()->tieneRol(1)) {
            return response()->json("Sin permiso", 200);
        }
        $oportunidad = Oportunidad::find($request->id);
        LogActividad::registrar(Auth::user(), 'Detalles de oportunidad', 9, null, null, null, 'Código: ' . $oportunidad->codigo_oportunidad);
        return response()->json($oportunidad, 200);
    }

    public function eliminar(Request $request)
    {
        if (!Auth::user()->tieneRol(6)) {
            return response()->json(array('mensaje' => 'Usuario sin permiso para eliminar la oportunidad', 'tipo' => 'danger'), 200);
        }
        $oportunidad = Oportunidad::find($request->id);
            $oportunidad->eliminado = true;
        $oportunidad->save();
        LogActividad::registrar(Auth::user(), 'Oportunidades', 3, $oportunidad->getTable(), $oportunidad);
        return response()->json(array('mensaje' => "La oportunidad $oportunidad->codigo_oportunidad se ha eliminado", 'tipo' => 'success'), 200);
    }

    public function actualizar(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'id' => 'required',
            'oportunidad' => 'required',
            'probabilidad' => 'required|max:5',
            'tipo_moneda' => 'required|max:1',
            'importe' => 'required',
            'margen' => 'required',
            'fecha_limite' => 'required',
            'grupo' => 'required',
            'tipo_negocio' => 'required',
            'cliente' => 'required'
        ]);
        if ($validar->fails()) {
            return response()->json(array('mensaje' => 'No se han ingresado datos en todos los campos necesarios para actualizar la oportunidad. Detalles: ' . implode(" ", $validar->errors()->all()), 'tipo' => 'danger'), 200);
        } else {
            $oportunidad = Oportunidad::find($request->id);
                if (!Auth::user()->tieneRol(5) && Auth::user()->id != $oportunidad->id_responsable) {
                    return response()->json(array('mensaje' => 'Usuario sin permiso para editar la oportunidad', 'tipo' => 'danger'), 200);
                }
                $oportunidad->nombre_contacto = $request->nombre_contacto;
                $oportunidad->telefono_contacto = $request->telefono_contacto;
                $oportunidad->correo_contacto = $request->correo_contacto;
                $oportunidad->cargo_contacto = $request->cargo_contacto;
                $oportunidad->reportado_por = $request->reportado_por;
                $oportunidad->id_entidad = $request->cliente;
                $oportunidad->oportunidad = $request->oportunidad;

                if (Auth::user()->tieneRol(4)) {
                    $oportunidad->id_responsable = $request->responsable;
                } else {
                    $oportunidad->id_responsable = Auth::user()->id;
                }

                if ($oportunidad->id_empresa == null) {
                    $abrev = Empresa::find(Auth::user()->id_empresa)->abreviado;
                    $subCodigo = $oportunidad->codigo_oportunidad;

                    $oportunidad->codigo_oportunidad = $abrev.$subCodigo;
                    $oportunidad->id_empresa = Auth::user()->id_empresa;
                }

                $oportunidad->probabilidad = $request->probabilidad;
                $oportunidad->moneda = $request->tipo_moneda;
                $oportunidad->importe = str_replace(',', '', $request->importe);
                $oportunidad->margen = $request->margen;
                $oportunidad->fecha_limite = $request->fecha_limite;
                $oportunidad->id_grupo = $request->grupo;
                $oportunidad->id_tipo_negocio = $request->tipo_negocio;
            $oportunidad->save();
            return response()->json(array('mensaje' => "La oportunidad $oportunidad->codigo_oportunidad se ha actualizado", 'tipo' => 'success'), 200);
        }
    }

    public function ingresarStatus(Request $request)
    {
        $oportunidad = Oportunidad::find($request->id);
        if (Auth::user()->tieneRol(7) || Auth::user()->id == $oportunidad->id_responsable) {
            $rules = array(
                'status' => 'required',
                'estado' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->route('mgcp.oportunidades.detalles', ['oportunidad' => $request->id])->withErrors($validator)->withInput();
            } else {
                try {
                    $registrado = false;
                    DB::beginTransaction();
                    if ($oportunidad->id_estado != $request->estado) {
                        $oportunidad->id_estado = $request->estado;
                        $oportunidad->save();
                    }
                    $status = new Status();
                        $status->detalle = trim($request->status);
                        $status->id_oportunidad = $request->id;
                        $status->id_estado = $request->estado;
                        $status->id_usuario = Auth::user()->id;
                    $status->save();

                    if ($request->hasFile('archivos')) {
                        $archivos = $request->file('archivos');
                        foreach ($archivos as $archivo) {
                            $nombreFinal = ArchivoHelper::limpiarNombre($archivo->getClientOriginalName());
                            $statusArchivo = new StatusArchivo();
                                $statusArchivo->nombre_archivo = $nombreFinal;
                                $statusArchivo->id_status = $status->id;
                            $statusArchivo->save();
                            Storage::putFileAs('mgcp/oportunidades/status/' . $statusArchivo->id, $archivo, $nombreFinal);
                        }
                    }
                    DB::commit();
                    $registrado = true;
                    LogActividad::registrar(Auth::user(), 'Detalles de oportunidad', 4, $status->getTable(), null, $status);
                    $request->session()->flash('alert-success', 'Se ha registrado el status');
                } catch (\Exception $ex) {
                    DB::rollBack();
                    $request->session()->flash('alert-danger', 'Hubo un problema al registrar el status. Por favor inténtelo de nuevo');
                }
            }
        } else {
            $request->session()->flash('alert-danger', 'Usuario sin permiso para registrar un status');
        }
        return redirect()->route('mgcp.oportunidades.detalles', ['oportunidad' => $request->id]);
    }

    public function ingresarActividad(Request $request)
    {
        $oportunidad = Oportunidad::find($request->id);
        if (Auth::user()->tieneRol(7) || Auth::user()->id == $oportunidad->id_responsable) {
            $rules = array(
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'detalle_actividad' => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $request->session()->flash('alert-danger', 'Falta ingresar un campo requerido');
            } else {
                if (Carbon::createFromFormat('d-m-Y', $request->fecha_inicio) > Carbon::createFromFormat('d-m-Y', $request->fecha_fin)) {
                    $request->session()->flash('alert-danger', 'La fecha de inicio no puede ser mayor a la fecha fin');
                } else {
                    $registrado = false;
                    DB::beginTransaction();
                    try {
                        $actividad = new Actividad();
                            $actividad->fecha_inicio = $request->fecha_inicio;
                            $actividad->fecha_fin = $request->fecha_fin;
                            $actividad->detalle = trim($request->detalle_actividad);
                            $actividad->id_oportunidad = $request->id;
                            $actividad->autor = Auth::user()->id;
                        $actividad->save();

                        if ($request->hasFile('archivos')) {
                            $archivos = $request->file('archivos');
                            foreach ($archivos as $archivo) {
                                $nombreFinal = ArchivoHelper::limpiarNombre($archivo->getClientOriginalName());
                                $actividadArchivo = new ActividadArchivo();
                                $actividadArchivo->nombre_archivo = $nombreFinal;
                                $actividadArchivo->id_actividad = $actividad->id;
                                $actividadArchivo->save();
                                Storage::putFileAs('mgcp/oportunidades/actividades/' . $actividadArchivo->id, $archivo, $nombreFinal);
                            }
                        }
                        DB::commit();
                        $registrado = true;
                        LogActividad::registrar(Auth::user(), 'Detalles de oportunidad', 4, $actividad->getTable(), null, $actividad);
                        $request->session()->flash('alert-success', 'Se ha registrado la actividad');
                    } catch (\Exception $ex) {
                        DB::rollBack();
                        $request->session()->flash('alert-danger', 'Hubo un problema al registrar la actividad: '.$ex->getMessage());
                    }
                }
            }
        } else {
            $request->session()->flash('alert-danger', 'Usuario sin permiso para registrar actividades');
        }
        return redirect()->route('mgcp.oportunidades.detalles', ['oportunidad' => $request->id]);
    }

    public function ingresarComentario(Request $request)
    {
        $oportunidad = Oportunidad::find($request->id);
        if (Auth::user()->tieneRol(7) || Auth::user()->id == $oportunidad->id_responsable) {
            $comentario = new Comentario;
                $comentario->comentario = trim($request->comentario);
                $comentario->id_oportunidad = $request->id;
                $comentario->autor = Auth::user()->id;
            $comentario->save();
            LogActividad::registrar(Auth::user(), 'Detalles de oportunidad', 4, $comentario->getTable(), null, $comentario);

            $request->session()->flash('alert-success', 'Se ha registrado el comentario');
        } else {
            $request->session()->flash('alert-danger', 'Usuario sin permiso para registrar comentarios');
        }
        return redirect()->route('mgcp.oportunidades.detalles', ['oportunidad' => $request->id]);
    }

    public function resumen()
    {
        if (!Auth::user()->tieneRol(2)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $responsables = OportunidadResumenHelper::obtenerResponsables(Auth::user()->id_empresa);
        $sumas = OportunidadResumenHelper::obtenerSumaImportes(Auth::user()->id_empresa);
        foreach ($responsables as $responsable) {
            $detalles[] = OportunidadResumenHelper::obtenerSumaImportesResponsable($responsable, Auth::user()->id_empresa);
        }
        LogActividad::registrar(Auth::user(), 'Resumen de oportunidades', 1);
        return view('mgcp.oportunidad.resumen')->with(compact('detalles', 'responsables', 'sumas'));
    }

    public function resumenDetallesMonto(Request $request)
    {
        $oportunidades = Oportunidad::with('estado', 'entidad')->where('eliminado', false)->where('id_estado', $request->codEstado)
            ->where('id_responsable', $request->corporativo)->where('moneda', $request->moneda)->orderBy('fecha_limite', 'asc')->get();
        return response()->json($oportunidades, 200);
    }

    public function resumenData()
    {
        $corporativos = OportunidadResumenHelper::obtenerResponsables(Auth::user()->id_empresa); //User::whereRaw('id IN (SELECT responsable_id FROM oportunidades WHERE eliminado=0)')->orderBy('name', 'asc')->get();

        $resumen_oportunidades = OportunidadResumenHelper::obtenerSumaImportes(Auth::user()->id_empresa);
        $pendientes = [];
        $ganados = [];
        $perdidos = [];
        $desestimados = [];
        foreach ($corporativos as $corporativo) {
            $pendientes[] = OportunidadResumenHelper::obtenerSumaImporteEstadoResponsable('1,2,3', $corporativo, Auth::user()->id_empresa);
            $ganados[] = OportunidadResumenHelper::obtenerSumaImporteEstadoResponsable('4', $corporativo, Auth::user()->id_empresa);
            $perdidos[] = OportunidadResumenHelper::obtenerSumaImporteEstadoResponsable('5', $corporativo, Auth::user()->id_empresa);
            $desestimados[] = OportunidadResumenHelper::obtenerSumaImporteEstadoResponsable('6', $corporativo, Auth::user()->id_empresa);
        }
        return response()->json(array('corporativos' => $corporativos, 'resumen_oportunidades' => $resumen_oportunidades, 'pendientes' => $pendientes, 'ganados' => $ganados, 'perdidos' => $perdidos, 'desestimados' => $desestimados), 200);
    }

    public function obtenerArchivos(Request $request)
    {
        switch ($request->tipo) {
            case 'status':
                $adjuntos = StatusArchivo::where('id_status', $request->id)->orderBy('nombre_archivo')->get();
                break;
            case 'actividades':
                $adjuntos = ActividadArchivo::where('id_actividad', $request->id)->orderBy('nombre_archivo')->get();
                break;
        }
        if ($adjuntos->count() > 0) {
            $contenido = '<ul>';
            foreach ($adjuntos as $fila) {
                $contenido .= '<li><a target="_blank" href="'.route('mgcp.base').'/oportunidades/descargar/'.$request->tipo.'/'.$fila->id.'/'.$fila->nombre_archivo.'">'.$fila->nombre_archivo.'</li>';
            }
            $contenido .= '</ul>';
        } else {
            $contenido = '<div class="text-center">Sin archivos</div>';
        }

        return response()->json(array('tipo' => 'success', 'mensaje' => $contenido), 200);
    }

    public function descargar($tipo,$id,$archivo)
    {
        return Storage::download('mgcp/oportunidades/'.$tipo.'/'.$id.'/'.$archivo);
    }

    public function agregarNotificacion(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'codigo' => 'required',
            'correo' => 'required',
            'dominio' => 'required'
        ]);
        if ($validar->fails()) {
            return response()->json(array('mensaje' => 'Falta un campo requerido para registrar el correo', 'tipo' => 'danger'), 200);
        }
        if (!in_array($request->dominio, array('okcomputer', 'proyectec'))) {
            return response()->json(array('mensaje' => 'El dominio seleccionado no es válido. Seleccione otro dominio e intente de nuevo', 'tipo' => 'danger'), 200);
        }
        $correo = $request->correo . '@' . $request->dominio . '.com.pe';
        $existe = Notificar::where('id_oportunidad', $request->codigo)->where('correo', $correo)->first();
        if ($existe != null) {
            return response()->json(array('mensaje' => 'El correo ingresado ya ha sido registrado anteriormente. Ingrese otro correo e intente de nuevo', 'tipo' => 'danger'), 200);
        }
        $oportunidad = Oportunidad::find($request->codigo);
        if ($correo == $oportunidad->responsable->email) {
            return response()->json(array('mensaje' => 'El correo ingresado es del autor de la oportunidad y actualmente recibe notificaciones.'), 200);
        }
        $notificar = new Notificar;
        $notificar->correo = $correo;
        $notificar->id_oportunidad = $request->codigo;
        $notificar->save();
        return response()->json(array('mensaje' => 'Se ha registrado el correo ' . $correo, 'id' => $notificar->id, 'tipo' => 'success'), 200);
    }

    public function retirarNotificacion(Request $request)
    {
        Notificar::destroy($request->codigo);
        return response()->json(array('mensaje' => 'Notificación eliminada', 'tipo' => 'success'), 200);
    }

    public function imprimir($id)
    {
        if (!Auth::user()->tieneRol(1)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $oportunidad = Oportunidad::find($id);
        if (Auth::user()->tieneRol(45) && $oportunidad->id_responsable != Auth::user()->id) {
            return redirect()->route('mgcp.oportunidades.lista');
        }

        $status = $oportunidad->status()->orderBy('created_at', 'asc')->get(); //$oportunidad->negocios;
        $actividades = $oportunidad->actividades()->orderBy('fecha_inicio', 'asc')->get();
        $comentarios = $oportunidad->comentarios()->orderBy('created_at', 'asc')->get();
        $nombreSistema = "MGCP";
        $view = View::make('mgcp.oportunidad.pdf.oportunidad', compact('oportunidad', 'status', 'actividades', 'comentarios', 'nombreSistema'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        return $pdf->stream($oportunidad->codigo_oportunidad . '.pdf');
    }

    public function crearDesdeOcPropia(Request $request)
    {
        DB::beginTransaction();
        try {
            
            if (!Auth::user()->tieneRol(4)) {
                $request->responsable = Auth::user()->id;
            }
            $ocView = OrdenCompraPropiaView::where('tipo', $request->tipo)->where('id',$request->idOc)->first();
            $oportunidad = OportunidadHelper::crearDesdeOcPropia($request->descripcion, $request->responsable, $ocView);
                $ordenCompra = $request->tipo=='am' ? OrdenCompraAm::find($request->idOc) : OrdenCompraDirecta::find($request->idOc);
                $ordenCompra->id_oportunidad = $oportunidad->id;
                $ordenCompra->id_corporativo = $oportunidad->id_responsable;
            $ordenCompra->save();

            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 4, $oportunidad->getTable(), null, $oportunidad);
            LogActividad::registrar(Auth::user(), 'Órdenes de compra propias', 6, null, null, null, 'Oportunidad: ' . $oportunidad->codigo_oportunidad . ', O/C: ' . $ocView->nro_orden);

            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha creado la oportunidad ' . $oportunidad->codigo_oportunidad . '. El sistema lo redireccionará al cuadro de costos...', 'id' => $oportunidad->id));
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(array('tipo' => 'danger', 'mensaje' => 'Hubo un problema al crear la oportunidad. Por favor inténtelo de nuevo.' . $ex));
        }
    }
}
