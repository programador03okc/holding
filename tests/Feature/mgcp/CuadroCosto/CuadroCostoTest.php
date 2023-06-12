<?php

namespace Tests\Feature\mgcp\CuadroCosto;

use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\Usuario\RolUsuario;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CuadroCostoTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_puede_acceder_formulario_lista_cuadros()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('mgcp.cuadro-costos.lista'));
        $response->assertSee("Su usuario no tiene permiso");
        
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 54
        ]);

        $response = $this->actingAs($user)->get(route('mgcp.cuadro-costos.lista'));
        $response->assertSee("Lista de cuadros");
    }

    public function test_puede_crear_cuadro_desde_oportunidad()
    {
        $user = User::factory()->create();
        $oportunidad = Oportunidad::factory()->create();
        $response = $this->actingAs($user)->get(route('mgcp.cuadro-costos.detalles',['id'=>$oportunidad->id]));
        $response->assertSee("Su usuario no tiene permiso");
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 54
        ]);
        $response = $this->actingAs($user)->get(route('mgcp.cuadro-costos.detalles',['id'=>$oportunidad->id]));
        $response->assertSee("Detalles de cuadro de Costos");
    }

    public function test_puede_crear_cuadro_desde_su_oportunidad_aun_sin_permiso()
    {
        $user = User::factory()->create();
        $oportunidad = Oportunidad::factory()->create([
            'id_responsable'=>$user->id
        ]);
        $response = $this->actingAs($user)->get(route('mgcp.cuadro-costos.detalles',['id'=>$oportunidad->id]));
        $response->assertSee("Detalles de cuadro de Costos");
    }
}
