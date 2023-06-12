<?php

namespace App\Models\mgcp\AcuerdoMarco\Producto;

use Illuminate\Database\Eloquent\Model;
use App\Models\mgcp\AcuerdoMarco\Producto\Categoria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model {

    use HasFactory, SoftDeletes;

    protected $table = 'mgcp_acuerdo_marco.productos_am';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function categoria() {
        return $this->hasOne(Categoria::class,'id','id_categoria');
    }

    public function getPrecioOkcAttribute($valor) {
        // return ($valor == null) ? 0 : $valor;
        return ($valor == null) ? 0 : ($this->attributes['moneda'] == 'USD' ? '$' : 'S/').number_format($valor, 2, '.', ',');
    }

    public function getPrecioProyAttribute($valor) {
        // return ($valor == null) ? 0 : $valor;
        return ($valor == null) ? 0 : ($this->attributes['moneda'] == 'USD' ? '$' : 'S/').number_format($valor, 2, '.', ',');
    }

    public function getPrecioSmartAttribute($valor) {
        // return ($valor == null) ? 0 : $valor;
        return ($valor == null) ? 0 : ($this->attributes['moneda'] == 'USD' ? '$' : 'S/').number_format($valor, 2, '.', ',');
    }

    public function getPrecioDoradoAttribute($valor) {
        // return ($valor == null) ? 0 : $valor;
        return ($valor == null) ? 0 : ($this->attributes['moneda'] == 'USD' ? '$' : 'S/').number_format($valor, 2, '.', ',');
    }

    public function getPrecioDezaAttribute($valor) {
        // return ($valor == null) ? 0 : $valor;
        return ($valor == null) ? 0 : ($this->attributes['moneda'] == 'USD' ? '$' : 'S/').number_format($valor, 2, '.', ',');
    }

    public function getPrecioProtecAttribute($valor) {
        // return ($valor == null) ? 0 : $valor;
        return ($valor == null) ? 0 : ($this->attributes['moneda'] == 'USD' ? '$' : 'S/').number_format($valor, 2, '.', ',');
    }
}
