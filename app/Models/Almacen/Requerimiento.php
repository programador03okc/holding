<?php

namespace App\Models\Almacen;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    use HasFactory;
    protected $table = 'almacen.alm_req';
    protected $primaryKey = 'id_requerimiento';
    public $timestamps = false;

    public static function obtenerCantidadRegistros($grupo,$idRequerimiento){
        $yyyy = date('Y', strtotime("now"));
        $num = Requerimiento::when(($grupo >0), function($query) use ($grupo,$idRequerimiento)  {
            return $query->Where([['id_grupo','=',$grupo],['id_requerimiento','<=',$idRequerimiento]]);
        })
        ->whereYear('fecha_registro', '=', $yyyy)
        ->count();
        return $num;
    }

    public static function crearCodigo($tipoRequerimiento,$idGrupo, $idRequerimiento){
        $documento = 'R'; //Prefijo para el codigo de requerimiento
        switch ($tipoRequerimiento) {
            case 1: # tipo MGCP
                $documento.='M';
                $num = Requerimiento::obtenerCantidadRegistros(2,$idRequerimiento);
                break;
            
            case 2: #tipo Ecommerce
                $documento.='E';
                $num = Requerimiento::obtenerCantidadRegistros(2,$idRequerimiento);
                break;
            
            case 3: case 4: case 5: case 6: case 7: #tipo:Bienes y Servicios, Compra para stock,Compra para activos,Compra para garantías,Otros
                if($idGrupo==1){
                    $documento.='A';
                    $num = Requerimiento::obtenerCantidadRegistros(1,$idRequerimiento); //tipo: BS, grupo: Administración
                }
                if($idGrupo==2){ 
                    $documento.='C';
                    $num = Requerimiento::obtenerCantidadRegistros(2,$idRequerimiento); //tipo: BS, grupo: Comercial
                }
                if($idGrupo==3){
                    $documento.='P';
                    $num = Requerimiento::obtenerCantidadRegistros(3,$idRequerimiento); //tipo: BS, grupo: Proyectos
                }
                break;
            
            default:
                $num = 0;
                break;
        }
        $yy = date('y', strtotime("now"));
        $correlativo= sprintf('%04d',$num);

        return "{$documento}-{$yy}{$correlativo}";

    }
}
