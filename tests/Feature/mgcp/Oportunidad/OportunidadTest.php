<?php

namespace Tests\Feature\mgcp\Oportunidad;

use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\Oportunidad\Estado;
use App\Models\mgcp\Oportunidad\Grupo;
use App\Models\mgcp\Oportunidad\Oportunidad;
use App\Models\mgcp\Oportunidad\TipoNegocio;
use App\Models\mgcp\Usuario\RolUsuario;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class OportunidadTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_puede_acceder_formulario_lista_oportunidades()
    {
        
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.lista'));
        $response->assertSee("Su usuario no tiene permiso");

        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 1
        ]);
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.lista'));
        $response->assertSee("Lista de oportunidad");
    }

    public function test_no_puede_ver_detalle_oportunidad_inexistente()
    {
        
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 1
        ]);
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.detalles', ['oportunidad' => 0]));
        $response->assertStatus(302);
    }

    public function test_puede_ver_detalles_oportunidad()
    {
        
        $oportunidad = Oportunidad::factory()->create();
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.detalles', ['oportunidad' => $oportunidad->id]));
        $response->assertSee("Su usuario no tiene permiso");
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 1
        ]);
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.detalles', ['oportunidad' => $oportunidad->id]));
        $response->assertSee("Detalles de oportunidad");
    }

    public function test_no_puede_ver_detalles_oportunidad_ajena_con_permiso_exclusivo()
    {
        
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 1
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 45
        ]);
        $oportunidad = Oportunidad::factory()->create();
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.detalles', ['oportunidad' => $oportunidad->id]));
        $response->assertSee("Su usuario no tiene permiso");
    }

    public function test_puede_acceder_formulario_nueva_oportunidad()
    {
        
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.nueva'));
        $response->assertSee("Su usuario no tiene permiso");

        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 3
        ]);
        $response = $this->actingAs($this->user)->get(route('mgcp.oportunidades.nueva'));
        $response->assertSee("Nueva oportunidad");
    }

    public function test_puede_eliminar_oportunidad()
    {
        Session::start();
        $oportunidad = Oportunidad::factory()->create();
        
        $response = $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.eliminar',
            ['id' => $oportunidad->id, '_token' => csrf_token()]
        ));
        //Sin permiso
        $response->assertJson([
            'tipo' => 'danger',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 6
        ]);
        $response = $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.eliminar',
            ['id' => $oportunidad->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        $this->assertDatabaseHas('mgcp_oportunidades.oportunidades', [
            'id' => $oportunidad->id,
            'eliminado' => true
        ]);
    }

    public function test_puede_actualizar_todas_las_oportunidad()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create();

        $enviar = [
            'id' => $oportunidad->id,
            'oportunidad' => 'TEST AUTO OKC',
            'probabilidad' => 'alta',
            'tipo_moneda' => 's',
            'importe' => '1000',
            'margen' => '10',
            'fecha_limite' => '10-10-2020',
            'grupo' => Grupo::first()->id,
            'tipo_negocio' => TipoNegocio::first()->id,
            'cliente' => $oportunidad->id_entidad,
            '_token' => csrf_token()
        ];
        $response = $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.actualizar',
            $enviar
        ));
        //Sin permiso
        $response->assertJson([
            'tipo' => 'danger',
        ]);

        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 5
        ]);
        $response = $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.actualizar',
            $enviar
        ));
        //Actualizar oportunidad de otro usuario porque tiene rol
        $response->assertJson([
            'tipo' => 'success',
        ]);
    }

    public function test_puede_actualizar_su_oportunidad_aun_sin_permiso()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create([
            'id_responsable' => $this->user->id
        ]);

        $enviar = [
            'id' => $oportunidad->id,
            'oportunidad' => 'TEST AUTO OKC',
            'probabilidad' => 'alta',
            'tipo_moneda' => 's',
            'importe' => '1000',
            'margen' => '10',
            'fecha_limite' => '10-10-2020',
            'grupo' => Grupo::first()->id,
            'tipo_negocio' => TipoNegocio::first()->id,
            'cliente' => $oportunidad->id_entidad,
            '_token' => csrf_token()
        ];
        $response = $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.actualizar',
            $enviar
        ));
        //La oportunidad es del usuario y puede actualizar su oportunidad aún sin permiso
        $response->assertJson([
            'tipo' => 'success',
        ]);
    }

    public function test_puede_registrar_oportunidad()
    {
        Session::start();
        
        $entidad = Entidad::factory()->create();
        $enviar = [
            'cliente' => $entidad->id,
            'oportunidad' => 'TEST AUTO OKC',
            'probabilidad' => 'alta',
            'fecha_limite' => '10-10-2020',
            'tipo_moneda' => 's',
            'importe' => '1000',
            'margen' => '10',
            'grupo' => Grupo::first()->id,
            'tipo_negocio' => TipoNegocio::first()->id,
            'nombre_contacto' => 'Wilmar',
            'telefono_contacto' => '953653788',
            'correo_contacto' => 'wilmar@garibaldi.com',
            'cargo_contacto' => 'Gerente',
            'reportado_por' => 'Wilmar',
            'responsable' => $this->user->id,
            'status' => 'STATUS',
            '_token' => csrf_token()
        ];
        $this->actingAs($this->user)->post(route('mgcp.oportunidades.registrar', $enviar));
        $this->assertDatabaseMissing('mgcp_oportunidades.oportunidades', [
            'oportunidad' => $enviar['oportunidad'],
            'id_entidad' => $entidad->id,
            'correo_contacto' => $enviar['correo_contacto']
        ]);

        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 3
        ]);
        $this->actingAs($this->user)->post(route('mgcp.oportunidades.registrar', $enviar));
        $this->assertDatabaseHas('mgcp_oportunidades.oportunidades', [
            'oportunidad' => $enviar['oportunidad'],
            'id_entidad' => $entidad->id,
            'correo_contacto' => $enviar['correo_contacto']
        ]);
    }

    public function test_puede_ingresar_status_cualquier_oportunidad()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create();
        $enviar = [
            'id' => $oportunidad->id,
            'status' => 'TEST AUTO OKC',
            'estado' => Estado::first()->id,
            '_token' => csrf_token()
        ];
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-status',
            $enviar
        ));
        $this->assertDatabaseMissing('mgcp_oportunidades.status', [
            'id_oportunidad' => $enviar['id'],
            'detalle' => $enviar['status'],
            'id_estado' => $enviar['estado']
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 7
        ]);
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-status',
            $enviar
        ));
        $this->assertDatabaseHas('mgcp_oportunidades.status', [
            'id_oportunidad' => $enviar['id'],
            'detalle' => $enviar['status'],
            'id_estado' => $enviar['estado']
        ]);
    }

    public function test_puede_ingresar_status_em_su_oportunidad_aun_sin_permiso()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create([
            'id_responsable' => $this->user->id
        ]);
        $enviar = [
            'id' => $oportunidad->id,
            'status' => 'TEST AUTO OKC',
            'estado' => Estado::first()->id,
            '_token' => csrf_token()
        ];
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-status',
            $enviar
        ));
        $this->assertDatabaseHas('mgcp_oportunidades.status', [
            'id_oportunidad' => $enviar['id'],
            'detalle' => $enviar['status'],
            'id_estado' => $enviar['estado']
        ]);
    }

    public function test_puede_ingresar_actividad_cualquier_oportunidad()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create();
        $enviar = [
            'id' => $oportunidad->id,
            'fecha_inicio' => '10-10-2020',
            'fecha_fin' => '10-11-2020',
            'detalle_actividad' => 'ACTIVIDAD TEST OKC',
            '_token' => csrf_token()
        ];
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-actividad',
            $enviar
        ));
        $this->assertDatabaseMissing('mgcp_oportunidades.actividades', [
            'id_oportunidad' => $enviar['id'],
            'autor' => $this->user->id
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 7
        ]);
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-actividad',
            $enviar
        ));
        $this->assertDatabaseHas('mgcp_oportunidades.actividades', [
            'id_oportunidad' => $enviar['id'],
            'autor' => $this->user->id
        ]);
    }

    public function test_puede_ingresar_actividad_en_su_oportunidad_aun_sin_permiso()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create([
            'id_responsable' => $this->user->id
        ]);
        $enviar = [
            'id' => $oportunidad->id,
            'fecha_inicio' => '10-10-2020',
            'fecha_fin' => '10-11-2020',
            'detalle_actividad' => 'ACTIVIDAD TEST OKC',
            '_token' => csrf_token()
        ];
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-actividad',
            $enviar
        ));
        $this->assertDatabaseHas('mgcp_oportunidades.actividades', [
            'id_oportunidad' => $enviar['id'],
            'autor' => $this->user->id
        ]);
    }

    public function test_puede_ingresar_comentario_cualquier_oportunidad()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create();
        $enviar = [
            'id' => $oportunidad->id,
            'comentario' => 'COMENTARIO AUTO OKC',
            '_token' => csrf_token()
        ];
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-comentario',
            $enviar
        ));
        $this->assertDatabaseMissing('mgcp_oportunidades.comentarios', [
            'id_oportunidad' => $enviar['id'],
            'autor' => $this->user->id
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $this->user->id,
            'id_rol' => 7
        ]);
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-comentario',
            $enviar
        ));
        $this->assertDatabaseHas('mgcp_oportunidades.comentarios', [
            'id_oportunidad' => $enviar['id'],
            'autor' => $this->user->id
        ]);
    }

    public function test_puede_ingresar_comentario_en_su_oportunidad_aun_sin_permiso()
    {
        Session::start();
        
        $oportunidad = Oportunidad::factory()->create([
            'id_responsable' => $this->user->id
        ]);
        $enviar = [
            'id' => $oportunidad->id,
            'comentario' => 'COMENTARIO AUTO OKC',
            '_token' => csrf_token()
        ];
        $this->actingAs($this->user)->post(route(
            'mgcp.oportunidades.ingresar-comentario',
            $enviar
        ));
        $this->assertDatabaseHas('mgcp_oportunidades.comentarios', [
            'id_oportunidad' => $enviar['id'],
            'autor' => $this->user->id
        ]);
    }
}
