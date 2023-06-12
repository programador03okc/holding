<?php

namespace App\Models\mgcp\AcuerdoMarco\Proforma;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DescargaProforma extends Model
{
    public $timestamps = false;
    protected $table = 'mgcp_acuerdo_marco.descargas_proformas';

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public static function registrar($idEmpresa, $idAcuerdo, $idCatalogo, $dias, $idUsuario)
    {
        $descarga = new DescargaProforma();
            $descarga->id_empresa = $idEmpresa;
            $descarga->id_acuerdo_marco = $idAcuerdo;
            $descarga->id_catalogo = $idCatalogo;
            $descarga->dias_antiguedad = $dias;
            $descarga->fecha_fin = new Carbon();
            $descarga->id_usuario = $idUsuario;
        $descarga->save();
    }
}
