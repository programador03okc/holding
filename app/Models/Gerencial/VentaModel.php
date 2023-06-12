<?php

namespace App\Models\Gerencial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaModel extends Model
{
    use HasFactory;

    protected $table = 'gerencial.venta';
    protected $primaryKey = 'id_venta';
    protected $fillable = [
        'id_venta', 'id_empresa', 'fecha', 'id_tipo_documento', 'nro_documento', 'id_cliente', 'codigo_producto', 'cantidad', 'id_unidad_medida', 
        'autor_factura_emitida', 'modenda', 'importe', 'nro_occ', 'fecha_emision', 'autor_comprobante_ingresado_softlink', 'nombre_vendedor', 'seguimiento', 
        'observacion', 'id_unidad_negocio', 'id_sector', 'id_division', 'codigo_centro_costo', 'fecha_registro', 'estado', 'id_periodo', 'id_centro_costo', 
        'nro_documento_vinculado', 'ruc_dni_cliente', 'id_documento_vinculado', 'codigo_empresa', 'tc'
    ];
}
