<?php

namespace App\Http\Controllers\mgcp\CuadroCosto;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoMicrosoft;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoProveedorView;
use App\Models\mgcp\CuadroCosto\Ajuste\MovimientoFondoMicrosoft;
use App\Models\mgcp\CuadroCosto\AmFilaTipo;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\CcAm;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CcAmProveedor;
use App\Models\mgcp\CuadroCosto\HistorialPrecio;
use App\Models\mgcp\CuadroCosto\CcAmFilaComentario;
use App\Models\mgcp\CuadroCosto\CcFilaMovimientoTransformacion;
use App\Models\mgcp\CuadroCosto\CuadroCostoView;
use App\Models\mgcp\CuadroCosto\Licencia;
use App\Models\mgcp\CuadroCosto\OrigenCosteo;
use App\Models\mgcp\Usuario\LogActividad;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CcAmController extends Controller
{
    private $nombreFormulario = 'Cuadro de presupuesto - Req. bienes para venta';
    private $camposNoEditables = array('id', 'id_cc', 'id_cc_am');

    public function actualizarCampoFila(Request $request)
    {
        $campo = $request->campo;
        $ccFila = CcAmFila::find($request->id);
        $cuadro = CuadroCosto::find($ccFila->id_cc_am);

        if ($ccFila->comprado) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }

        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) != 'ninguno' && !in_array($request->campo, $this->camposNoEditables)) {
            $valor = htmlspecialchars_decode(strip_tags(trim(str_replace(['&nbsp;'], "", $request->valor))));
            if (in_array($campo, array('pvu_oc', 'flete_oc', 'cantidad', 'garantia'))) {
                $valor = str_replace(',', '', $valor);
            }

            //Si se va a actualizar la cantidad se debe ver si no va a exceder el consumo de fondo de proveedores
            if ($campo == 'cantidad') {
                $idFondo = $ccFila->amProveedor == null ? null : $ccFila->amProveedor->id_fondo_proveedor;
                if ($idFondo != null) {
                    DB::beginTransaction();
                    $cantidadAnterior = $ccFila->cantidad;
                    //Se pone en 0 para liberar el uso del fondo de proveedores
                    $ccFila->cantidad = 0;
                    $ccFila->save();
                    $fondo = FondoProveedorView::find($idFondo);
                    if ($fondo->cantidad_disponible < intval($valor)) {
                        DB::rollBack();
                        return response()->json(array('tipo' => 'danger', 'mensaje' => "No hay suficientes fondos disponibles: La cantidad ingresada es $valor y la cantidad de fondos disponibles es de $fondo->cantidad_disponible. Ingrese una cantidad menor o elimine el fondo de proveedor y vuelva a intentarlo.", 'valor' => $cantidadAnterior), 200);
                    } else {
                        DB::commit();
                    }
                }
            }

            if ($campo == 'pvu_oc') {
                if ($valor == '') {
                    $valor = null;
                } else {
                    $stNumero = strpos($valor, '.');
                    if ($stNumero == true) {
                        $valor = $valor;
                    } else {
                        $valor = $valor.'.00';
                    }
                }
            }
            
            if ($campo == 'id_origen_costeo') {
                $dataAnterior[$campo] = OrigenCosteo::find($ccFila->$campo)->origen;
                $dataNueva[$campo] = OrigenCosteo::find($valor)->origen;
            } else {
                $dataAnterior[$campo] = $ccFila->$campo ?? '';
                $dataNueva[$campo] = $valor;
            }

            $ccFila->id_ultimo_usuario = Auth::user()->id;
            $ccFila->$campo = $valor == '' ? null : $valor;
            $ccFila->save();

            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $ccFila->getTable(),$dataAnterior, $dataNueva,'CDP: '.$cuadro->oportunidad->codigo_oportunidad.', ID fila: '.$request->id);

            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha actualizado el valor'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function actualizarCompraFila(Request $request)
    {
        $ccFila = CcAmFila::find($request->idFila);
        $cuadro = $ccFila->cuadroAm->cuadroCosto;
        //Si usuario es de compras y el cuadro no está pendiente de aprobación ni finalizado
        if (Auth::user()->tieneRol(46) && !in_array($cuadro->estado_aprobacion, [2, 4])) { //if (CuadroCosto::tipoEdicion(CuadroCosto::find($ccFila->id_cc_am), Auth::user()) != 'ninguno') {
                $dataAnterior['comprado'] = $ccFila->comprado;
                $dataNueva['comprado'] = !$ccFila->comprado;
                $ccFila->comprado = !$ccFila->comprado;
                $ccFila->id_ultimo_usuario = Auth::user()->id;
            $ccFila->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $ccFila->getTable(), $dataAnterior, $dataNueva,'CDP: '.$cuadro->oportunidad->codigo_oportunidad.', ID fila: '.$request->idFila);

            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha marcado la fila como ' . ($ccFila->comprado ? 'comprada' : 'no comprada')), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function actualizarCampo(Request $request)
    {
        $campo = $request->campo;

        if (CuadroCosto::tipoEdicion(CuadroCosto::find($request->id), Auth::user()) == 'corporativo' && !in_array($request->campo, $this->camposNoEditables)) {
            $ccFila = CcAm::find($request->id);
                $valor = str_replace("&nbsp;", "", htmlentities(strip_tags(trim($request->valor)), ENT_COMPAT, 'utf-8'));
                $dataAnterior[$campo] = $ccFila->$campo;
                $dataNueva[$campo] = $valor;
                $ccFila->$campo = $valor == '' ? null : $valor;
            $ccFila->save();
            $nombreCampo = "";

            switch ($campo) {
                case 'fecha_formalizacion':
                    $nombreCampo = "la fecha de formalización";
                    break;
                case 'fecha_entrega':
                    $nombreCampo = "la fecha de entrega";
                    break;
                case 'moneda_pvu':
                    $nombreCampo = "la moneda del P.V.U.";
                    break;
            }
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $ccFila->getTable(), $dataAnterior, $dataNueva,'CDP: '.$ccFila->cuadroCosto->oportunidad->codigo_oportunidad.', ID fila: '.$request->id);
            return response()->json(array('tipo' => 'success', 'mensaje' => "Se ha actualizado $nombreCampo"), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function obtenerDetallesFila(Request $request)
    {
        $fila = CcAmFila::find($request->id);
        return response()->json(array('tipo' => 'success', 'fila' => $fila), 200);
    }

    public function actualizarCampoProveedor(Request $request)
    {
        //No se implementa el log porque ya existe un historial de precios
        $campo = $request->campo;
        $ccAm = CcAmProveedor::find($request->idFila);
        $fila = CcAmFila::find($ccAm->id_fila);

        if (CuadroCosto::tipoEdicion(CuadroCosto::find($fila->id_cc_am), Auth::user()) != 'ninguno' && !in_array($request->campo, $this->camposNoEditables)) {
            switch ($campo) {
                case 'precio':
                    $ccAm->$campo = str_replace(',', '', $request->valor);
                    $historial = new HistorialPrecio();
                    $historial->tabla = 'ccAm';
                    $historial->id_fila = $request->idFila;
                    $historial->precio = str_replace(',', '', $request->valor);
                    $historial->id_responsable = Auth::user()->id;
                    $historial->fecha = new Carbon();
                    $historial->save();
                    break;
                case 'plazo':
                    $ccAm->$campo = $request->valor;
                    break;
                case 'flete':
                    $ccAm->$campo = str_replace(',', '', $request->valor);
                    break;
            }
            $ccAm->save();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Ok'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function eliminarProveedorFila(Request $request)
    {
        $ccAmProv = CcAmProveedor::find($request->idFilaProveedor);
        $fila = CcAmFila::find($ccAmProv->id_fila);
        $cuadro = CuadroCosto::find($fila->id_cc_am);

        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) != 'ninguno') {
            DB::beginTransaction();
                $fila->proveedor_seleccionado = null;
                $fila->save();
                HistorialPrecio::where('tabla', 'ccAm')->where('id_fila', $request->idFilaProveedor)->delete();

                $filaProveedor = CcAmProveedor::find($request->idFilaProveedor);
                    LogActividad::registrar(Auth::user(), $this->nombreFormulario, 3, $filaProveedor->getTable(), $filaProveedor,null,'CDP: '.$cuadro->oportunidad->codigo_oportunidad);
                $filaProveedor->delete();
            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la fila'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function obtenerHistorialPrecios(Request $request)
    {
        $historial = HistorialPrecio::with('user')->where('id_fila', $request->idFila)->where('tabla', 'ccAm')->orderBy('fecha', 'desc')->get();
        $cdp = CcAm::find(CcAmFila::find(CcAmProveedor::find($request->idFila)->id_fila)->id_cc_am)->cuadroCosto->oportunidad->codigo_oportunidad;
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 12, null, null, null, 'CDP: ' . $cdp.', ID fila: '.$request->idFila);
        return response()->json($historial, 200);
    }

    public function obtenerLicencias(Request $request)
    {
        $licencias = Licencia::orderBy('descripcion', 'ASC')->get();
        LogActividad::registrar(Auth::user(), 'Licencias para cuadros de presupuesto', 1);
        return response()->json(array('tipo' => 'success', 'licencias' => $licencias), 200);
    }

    public function obtenerFondosMS(Request $request)
    {
        $fondos = FondoMicrosoft::where('estado', 1)->orderBy('descripcion', 'asc')->get();
        return response()->json(array('tipo' => 'success', 'fondos' => $fondos), 200);
    }

    public function agregarFila(Request $request)
    {
        $cuadro = CuadroCosto::find($request->idCuadro);
        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) == 'corporativo') {
            $fila = new CcAmFila();
            $fila->id_ultimo_usuario = Auth::user()->id;
            $fila->id_cc_am = $request->idCuadro;
            $fila->id_origen_costeo = 1;
            $fila->comprado = 0;
            $fila->id_tipo_fila = $request->tipoFila;
            if ($request->idLicencia > 0) {
                $licencia = Licencia::find($request->idLicencia);
                $fila->marca = $licencia->marca;
                $fila->part_no  = $licencia->part_no;
                $fila->descripcion = $licencia->descripcion;
            }
            if ($request->idFondo > 0) {
                $fondoMS = FondoMicrosoft::find($request->idFondo);
                $fila->marca = 'MICROSOFT';
                $fila->part_no  = $fondoMS->part_no;
                $fila->descripcion = $fondoMS->descripcion;
                $fila->cantidad = 1;
            }
            $fila->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 11, $fila->getTable(), null, null,'CDP: ' . $cuadro->oportunidad->codigo_oportunidad . ', ID fila: ' . $fila->id);

            if ($request->idFondo > 0) {
                $cuadro = CuadroCosto::find($fila->id_cc_am);
                $cuadroView = CuadroCostoView::find($cuadro->id);

                $fondo = new MovimientoFondoMicrosoft();
                    $fondo->tipo_movimiento = 3;
                    $fondo->tipo_bolsa_id = $fondoMS->tipo_bolsa->id;
                    $fondo->fondo_microsoft_origen_id = $fondoMS->id;
                    $fondo->motivo = $cuadroView->codigo_oportunidad;
                    $fondo->fecha = new Carbon();
                    $fondo->id_cc_fila = $fila->id;
                    $fondo->id_usuario = Auth::user()->id;
                    $fondo->aprobacion = true;
                    $fondo->importe = -($fondoMS->importe);
                $fondo->save();

                $fondoMS->update(['estado' => 2]);

                $proveedor = new CcAmProveedor;
                    $proveedor->id_fila = $fila->id;
                    $proveedor->id_proveedor = 243;
                    $proveedor->precio = $fondo->importe;
                    $proveedor->moneda = 'd';
                    $proveedor->plazo = 1;
                    $proveedor->flete = 0;
                    $proveedor->comentario = null;
                    $proveedor->id_fondo_proveedor = null;
                $proveedor->save();

                $filaPro = CcAmFila::find($fila->id);
                    $filaPro->proveedor_seleccionado = $proveedor->id;
                $filaPro->save();
            }

            return response()->json(array('tipo' => 'success', 'data' => $this->generarFilaCuadro($fila)), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    private function generarFilaCuadro($fila)
    {
        $contEditable = '';
        $colorFila = '';
        $contEditableMS = '';
        $colorFilaMS = '';
        if ($fila->id_tipo_fila == 1) {
            $contEditable = 'contenteditable="true"';
            $colorFila = 'success';
        }

        if ($fila->id_tipo_fila != 4) {
            $contEditableMS = 'contenteditable="true"';
            $colorFilaMS = 'success';
        }

        $cant = ($fila->cantidad != null) ? $fila->cantidad : '';
        $pvu = ($fila->pvu_oc != null) ? $fila->pvu_oc : '';

        $origenesCosteo = OrigenCosteo::get();
        $data = '
        <tr>
            <td>'.AmFilaTipo::find($fila->id_tipo_fila)->tipo_abreviado.'</td>
            <td data-id="' . $fila->id . '" class="text-center ' . $colorFila . ' numero-parte escape" ' . $contEditable . ' spellcheck="false">' . $fila->part_no . '</td>
            <td data-id="' . $fila->id . '" data-campo="marca" class="' . $colorFila . ' marca text-center" ' . $contEditable . ' spellcheck="false">' . $fila->marca . '</td>
            <td data-id="' . $fila->id . '" data-campo="descripcion" class="' . $colorFila . ' descripcion" ' . $contEditable . ' spellcheck="false">' . $fila->descripcion . '</td>
            <td data-id="' . $fila->id . '" data-campo="pvu_oc" class="text-right ' . $colorFilaMS . ' decimal pvu-oc" ' . $contEditableMS . '>'. $pvu .'</td>
            <td data-id="' . $fila->id . '" data-campo="flete_oc" class="text-right ' . $colorFilaMS . ' decimal flete-oc" ' . $contEditableMS . '></td>
            <td data-id="' . $fila->id . '" data-campo="cantidad" class="text-center ' . $colorFilaMS . ' entero cantidad" ' . $contEditableMS . '>'. $cant .'</td>
            <td data-id="' . $fila->id . '" data-campo="garantia" class="text-center ' . $colorFilaMS . ' entero garantia tab" ' . $contEditableMS . '></td>
            <td class="' . $colorFilaMS . '">
            <select data-id="' . $fila->id . '" data-campo="id_origen_costeo" style="font-size: x-small;" class="form-control input-sm origen-costeo">';
        foreach ($origenesCosteo as $origen) {
            $data .= '<option value="' . $origen->id . '">' . $origen->origen . '</option>';
        }
        $data .= '</select>
            </td>
            <td data-id="' . $fila->id . '" class="proveedor-nombre info"></td>
            <td data-id="' . $fila->id . '" class="proveedor-precio info text-right"></td>
            <td data-id="' . $fila->id . '" class="proveedor-plazo info text-center"></td>
            <td data-id="' . $fila->id . '" class="proveedor-flete info text-right"></td>
            <td data-id="' . $fila->id . '" class="proveedor-fondo info text-center"></td>
            <td class="text-right costo-total"></td>
            <td class="text-right costo-total-convertido"></td>
            <td class="text-right flete-total"></td>
            <td class="text-right costo-flete-total"></td>
            <td class="text-right monto-adjudicado"></td>
            <td class="text-right ganancia"></td>
            <td class="text-center">
                <div class="btn-group" role="group">';
        if ($fila->id_tipo_fila == 1) {
            $data .= '<button data-id="' . $fila->id . '" title="Transformación" class="btn btn-xs transformacion btn-default"><span class="glyphicon glyphicon-transfer"></span></button>';
        }
        $data .= '<button data-id="' . $fila->id . '" title="Comentarios" class="btn btn-xs comentarios"><span class="glyphicon glyphicon-comment"></span></button> 
                    <button title="Eliminar" data-id="' . $fila->id . '" class="btn btn-xs eliminar"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                </div>
            </td>
        </tr>';
        return $data;
    }

    public function eliminarFila(Request $request)
    {
        $fila = CcAmFila::find($request->idFila);
        $cuadro = CuadroCosto::find($fila->id_cc_am);

        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) == 'corporativo') {
            DB::beginTransaction();
                $fila->proveedor_seleccionado=null;
                $fila->save();
                $filasProveedor = CcAmProveedor::where('id_fila', $request->idFila)->get();

                foreach ($filasProveedor as $filaProveedor) {
                    HistorialPrecio::where('tabla', 'ccAm')->where('id_fila', $filaProveedor->id)->delete();
                    $filaProveedor->delete();
                }
                CcAmFilaComentario::where('id_fila', $request->idFila)->delete();
                CcFilaMovimientoTransformacion::where('id_fila_ingresa', $fila->id)->update(['id_fila_ingresa' => null]);
                CcFilaMovimientoTransformacion::where('id_fila_base', $fila->id)->delete();
                LogActividad::registrar(Auth::user(), $this->nombreFormulario, 3, $fila->getTable(), $fila, null, 'CDP: '.$cuadro->oportunidad->codigo_oportunidad);
                $fila->delete();

                $movFondoMS = MovimientoFondoMicrosoft::where('id_cc_fila', $request->idFila);
                if ($movFondoMS->count() > 0) {
                    $dataFondoMS = $movFondoMS->first();
                    FondoMicrosoft::where('id', $dataFondoMS->fondo_microsoft_origen_id)->update(['estado' => 1]);
                    $movFondoMS->delete();
                }
            DB::commit();
            return response()->json(array('tipo' => 'success', 'mensaje' => 'Se ha eliminado la fila'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function obtenerProveedoresFila(Request $request)
    {
        $proveedores = CcAmProveedor::with(['proveedor', 'fondoProveedor'])->where('id_fila', $request->idFila)->orderBy('id', 'desc')->get();
        $ccAmFila = CcAmFila::find($request->idFila);
        $provSeleccionado = $ccAmFila->proveedor_seleccionado;
        $cdp = $ccAmFila->cuadroAm->cuadroCosto->oportunidad->codigo_oportunidad;
        LogActividad::registrar(Auth::user(), 'Proveedores para CDP - Bienes para venta', 9, null, null, null, 'CDP: ' . $cdp.', ID fila: ' . $request->idFila);
        return response()->json(array('proveedores' => $proveedores, 'provSeleccionado' => $provSeleccionado), 200);
    }

    public function agregarProveedorFila(Request $request)
    {
        $fila = CcAmFila::find($request->idFila);
        $cuadro = CuadroCosto::find($fila->id_cc_am);
        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) != 'ninguno') {

            if ($request->fondo != 0) {
                $fondo = FondoProveedorView::find($request->fondo);
                if ($fondo->cantidad_disponible < $fila->cantidad) {
                    return response()->json(array('tipo' => 'error', 'titulo' => "No hay suficientes fondos disponibles", "texto" => "La cantidad a comprar es de $fila->cantidad y la cantidad de fondos disponibles es de $fondo->cantidad_disponible. Seleccione otro fondo y vuelva a intentarlo."), 200);
                }
            } else {
                $fondo = null;
            }

            DB::beginTransaction();
            $proveedor = new CcAmProveedor;
                $proveedor->id_fila = $request->idFila;
                $proveedor->id_proveedor = $request->proveedor;
                $proveedor->precio = str_replace(',', '', $request->precio);
                $proveedor->moneda = $request->moneda;
                $proveedor->plazo = $request->plazo;
                $proveedor->flete = $request->flete;
                $proveedor->comentario = $request->comentario;
                $proveedor->id_fondo_proveedor = $request->fondo == 0 ? null : $request->fondo;
            $proveedor->save();

            LogActividad::registrar(Auth::user(), 'Proveedores para CDP - Bienes para venta', 4, $proveedor->getTable(), null, $proveedor,'CDP: '.$cuadro->oportunidad->codigo_oportunidad);

            $historial = new HistorialPrecio;
                $historial->tabla = 'ccAm';
                $historial->id_fila = $proveedor->id;
                $historial->precio = str_replace(',', '', $request->precio);
                $historial->id_responsable = Auth::user()->id;
                $historial->fecha = new Carbon;
            $historial->save();
            DB::commit();
            return response()->json(array('tipo' => 'success', 'proveedor' => $proveedor, 'fondo' => $fondo), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function seleccionarMejorPrecio(Request $request)
    {
        $proveedores = CcAmProveedor::where('id_fila', $request->idFila)->get();
        $fila = CcAmFila::find($request->idFila);
        $cuadro = CuadroCosto::find($fila->id_cc_am);
        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) != 'ninguno') {

            //Definir el monto menor inicial
            $menorPrecio = null;
            $id = 0;
            if ($proveedores->count() > 0) {
                foreach ($proveedores as $proveedor) {
                    $precio = ($proveedor->moneda == 'd' ? ($proveedor->precio * $cuadro->tipo_cambio) : $proveedor->precio) + $proveedor->flete;
                    $fondo = $proveedor->fondoProveedor;
                    if ($fondo != null) {
                        //Conversión del fondo de proveedor de acuerdo a la moneda del proveedor
                        if ($proveedor->moneda == $fondo->moneda) {
                            $precio -= $fondo->valor_unitario;
                        } else {
                            $precio -= ($proveedor->moneda == 's' ? ($fondo->valor_unitario * $cuadro->tipo_cambio) : ($fondo->valor_unitario / $cuadro->tipo_cambio));
                        }
                    }
                    if ($menorPrecio == null || $precio < $menorPrecio) {
                        $menorPrecio = $precio;
                        $id = $proveedor->id;
                    }
                }

                DB::beginTransaction();
                    $proveedor = CcAmProveedor::find($id);
                    $dataAnterior['proveedor_seleccionado'] = $fila->proveedor_seleccionado;
                    
                    if ($proveedor->id_fondo_proveedor != null) {
                        //Se borra el proveedor seleccionado para que no se considere el fondo de proveedor de la fila
                        $dataAnterior['proveedor_seleccionado'] = $fila->proveedor_seleccionado;
                        $fila->proveedor_seleccionado = null;
                        $fila->save();
                        $fondoView = FondoProveedorView::find($proveedor->id_fondo_proveedor);
                        if ($fondoView->cantidad_disponible < $fila->cantidad) {
                            DB::rollBack();
                            $razonSocial = $proveedor->proveedor->razon_social;
                            return response()->json(array('tipo' => 'error', 'titulo' => "El mejor precio (proveedor: $razonSocial) tiene problemas", "texto" => "La cantidad de productos a comprar es de $fila->cantidad y la cantidad de fondos disponibles de $fondoView->descripcion es de $fondoView->cantidad_disponible. Seleccione otro proveedor de forma manual o agregue unidades al fondo y vuelva a intentarlo."), 200);
                        }
                    }
                    //Actualiza el proveedor seleccionado
                    $fila->proveedor_seleccionado = $id;
                    $dataNueva['proveedor_seleccionado'] = $fila->proveedor_seleccionado;
                    $fila->save();
                    LogActividad::registrar(Auth::user(), 'Proveedores para CDP - Bienes para venta', 2, $fila->getTable(), $dataAnterior, $dataNueva,'CDP: '.$cuadro->oportunidad->codigo_oportunidad);
                DB::commit();
                return response()->json(array('tipo' => 'success', 'titulo' => 'ok', 'id' => $id), 200);
            } else {
                return response()->json(array('tipo' => 'error', 'titulo' => 'No hay precios registrados'), 200);
            }
        }
    }

    public function seleccionarProveedorFila(Request $request)
    {
        $fila = CcAmFila::find($request->idFila);
        $cuadro = CuadroCosto::find($fila->id_cc_am);

        if (CuadroCosto::tipoEdicion($cuadro, Auth::user()) != 'ninguno') {
            DB::beginTransaction();
                $filaProveedor = CcAmProveedor::find($request->idFilaProveedor);

                if ($filaProveedor->id_fondo_proveedor != null) {
                    //Se borra el proveedor para que no se considere el fondo de proveedor de la fila
                    $fila->proveedor_seleccionado = null;
                    $fila->save();
                    $fondoProveedor = FondoProveedorView::find($filaProveedor->id_fondo_proveedor);

                    if ($fondoProveedor->cantidad_disponible < $fila->cantidad) {
                        DB::rollBack();
                        return response()->json(array('tipo' => 'error', 'titulo' => "No hay suficientes fondos disponibles", "texto" => "La cantidad de productos a comprar es de $fila->cantidad y la cantidad de fondos disponibles de $fondoProveedor->descripcion es de $fondoProveedor->cantidad_disponible. Seleccione otro proveedor o agregue unidades al fondo y vuelva a intentarlo."), 200);
                    }
                }
                $dataAnterior['proveedor_seleccionado'] = $fila->proveedor_seleccionado;
                $dataNueva['proveedor_seleccionado'] = $request->idFilaProveedor;
                $fila->proveedor_seleccionado = $request->idFilaProveedor;
                $fila->save();
                LogActividad::registrar(Auth::user(), 'Proveedores para CDP - Bienes para venta', 2, $fila->getTable(), $dataAnterior, $dataNueva,'CDP: '.$cuadro->oportunidad->codigo_oportunidad);
            DB::commit();
            return response()->json(array('tipo' => 'success', 'titulo' => 'ok'), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'titulo' => 'El cuadro es de sólo lectura'), 200);
        }
    }

    public function buscarNroParte(Request $request)
    {
        $filaActualizar = CcAmfila::find($request->idFila);

        if (CuadroCosto::tipoEdicion(CuadroCosto::find($filaActualizar->id_cc_am), Auth::user()) == 'corporativo') {
            $dataAnterior['part_no'] = $filaActualizar->part_no;
            $dataAnterior['marca'] = $filaActualizar->marca;
            $dataAnterior['descripcion'] = $filaActualizar->descripcion;

            $filaActualizar->part_no = str_replace("&nbsp;", "", htmlentities(strip_tags(trim(mb_strtoupper($request->criterio))), ENT_COMPAT, 'utf-8'));
            $dataNueva['part_no'] = $filaActualizar->part_no;

            if ($filaActualizar->part_no != '') {
                if ($filaActualizar->id_tipo_fila == 4) {
                    $fondo = FondoMicrosoft::where('part_no', $filaActualizar->part_no)->orderBy('id', 'desc')->first();
                    if ($fondo != null) {
                        $filaActualizar->descripcion = $fondo->descripcion;
                        $filaActualizar->marca = 'MICROSOFT';
                        $dataNueva['marca'] = $filaActualizar->marca;
                        $dataNueva['descripcion'] = $filaActualizar->descripcion;
                    }
                } else {
                    $producto = Producto::where('part_no', $filaActualizar->part_no)->orderBy('id', 'desc')->first();
                    if ($producto !== null) {
                        $filaActualizar->descripcion = $producto->descripcion;
                        $filaActualizar->marca = $producto->marca;
                        $dataNueva['marca'] = $filaActualizar->marca;
                        $dataNueva['descripcion'] = $filaActualizar->descripcion;
                    } else {
                        $filaCcAm = CcAmfila::where('part_no', $filaActualizar->part_no)->orderBy('id', 'desc')->first();
                        if ($filaCcAm != null) {
                            $filaActualizar->descripcion = $filaCcAm->descripcion;
                            $filaActualizar->marca = $filaCcAm->marca;
                            $dataNueva['marca'] = $filaActualizar->marca;
                            $dataNueva['descripcion'] = $filaActualizar->descripcion;
                        }
                    }
                }
            }
            $filaActualizar->save();
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $filaActualizar->getTable(), $dataAnterior, $dataNueva,'CDP: '.$filaActualizar->cuadroAm->cuadroCosto->oportunidad->codigo_oportunidad.', ID fila: '.$request->idFila);
            return response()->json(array('tipo' => 'success', 'marca' => $filaActualizar->marca, 'descripcion' => $filaActualizar->descripcion), 200);
        } else {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'El cuadro de costo está en modo de sólo lectura'), 200);
        }
    }

    public function listarComentarios(Request $request)
    {
        $codigoCdp = CcAmFila::find($request->idFila)->cuadroAm->cuadroCosto->oportunidad->codigo_oportunidad;
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 13, null, null, null, 'CDP: '.$codigoCdp.', ID fila: '.$request->idFila);
        $comentarios = CcAmFilaComentario::with('usuario')->where('id_fila', $request->idFila)->orderBy('fecha', 'ASC')->get();
        return response()->json($comentarios, 200);
    }

    public function registrarComentario(Request $request)
    {
        $fila = CcAmFila::find($request->idFila);
        if (is_null($fila)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'No existe la fila'));
        }

        if (empty($request->comentario)) {
            return response()->json(array('tipo' => 'error', 'mensaje' => 'Ingrese un comentario'));
        }

        $fila = new CcAmFilaComentario();
            $fila->fecha = new Carbon();
            $fila->id_usuario = Auth::user()->id;
            $fila->comentario = $request->comentario;
            $fila->id_fila = $request->idFila;
        $fila->save();
        
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 4, $fila->getTable(), null, $fila,'CDP: '.CcAmFila::find($request->idFila)->cuadroAm->cuadroCosto->oportunidad->codigo_oportunidad);
        return response()->json(array('tipo' => 'success', 'autor' => Auth::user()->name, 'fecha' => $fila->fecha, 'comentario' => $fila->comentario, 'mensaje' => 'Se ha registrado el comentario'), 200);
    }
}
