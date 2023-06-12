<?php

namespace App\Http\Controllers\mgcp\AcuerdoMarco;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Helpers\mgcp\PeruComprasHelper;
use App\Models\mgcp\Usuario\LogActividad;

class EmpresaController extends Controller
{
    private $nombreFormulario = 'Cambiar claves de empresas';

    public function cambiarClaves()
    {
        if (!Auth::user()->tieneRol(40)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $empresa = Empresa::find(Auth::user()->id_empresa);
        LogActividad::registrar(Auth::user(), $this->nombreFormulario, 1);
        return view('mgcp.acuerdo-marco.empresa.cambiar-claves', get_defined_vars());
    }

    public function obtenerSemaforo($idEmpresa)
    {
        $empresa = Empresa::find($idEmpresa);
        $portal = new PeruComprasHelper();
        if ($portal->login($empresa, 3)) { // ealvarez 2
            $data = $portal->enviarData('', 'https://www.catalogos.perucompras.gob.pe/Accesos/ObtenerIndicadorSemaforoProveedor');
            $empresa->indicador_semaforo = substr($data, 0, 1);
            $empresa->save();
        }
    }

    public function actualizarClaves(Request $request)
    {
        if (!Auth::user()->tieneRol(40)) {
            return view('mgcp.usuario.sin_permiso');
        }
        $empresa = Empresa::find($request->empresa);
        $cambio = false;
        if ($request->clave_uno != null) {
            $cambio = true;
            $empresa->password = $request->clave_uno;
        }

        if ($request->clave_dos != null) {
            $cambio = true;
            $empresa->password2 = $request->clave_dos;
        }

        if ($request->clave_tres != null) {
            $cambio = true;
            $empresa->password3 = $request->clave_tres;
        }
        
        if ($cambio) {
            LogActividad::registrar(Auth::user(), $this->nombreFormulario, 2, $empresa->getTable(),'Cambio de clave en empresa: '.$empresa->empresa,'Cambio de clave en empresa: '.$empresa->empresa);
            $empresa->save();
            
            $request->session()->flash('alert-success', 'Clave(s) de empresa ' . $empresa->empresa . ' cambiada(s)');
        } else {
            $request->session()->flash('alert-info', 'No se han registrado cambios en las claves de la empresa ' . $empresa->empresa);
        }
        return redirect()->route('mgcp.acuerdo-marco.empresas.cambiar-claves');
    }
}
