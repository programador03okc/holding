<?php

namespace Tests\Feature\mgcp\CuadroCosto;

use App\Http\Controllers\mgcp\CuadroCosto\CuadroCostoController;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoIngreso;
use App\Models\mgcp\CuadroCosto\CcAm;
use App\Models\mgcp\CuadroCosto\CcAmFila;
use App\Models\mgcp\CuadroCosto\CcAmFilaComentario;
use App\Models\mgcp\CuadroCosto\CcAmProveedor;
use App\Models\mgcp\CuadroCosto\CuadroCosto;
use App\Models\mgcp\CuadroCosto\Proveedor;
use App\Models\mgcp\Usuario\RolUsuario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CcAmTest extends TestCase
{
    use DatabaseTransactions;

    protected function runTest(): void
    {
        $this->markTestSkipped('Migrar de Venta a AM');
    }

    public function test_puede_obtener_comentarios()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $comentario1 = CcAmFilaComentario::factory()->create([
            'fecha' => Carbon::now()->addDays(1),
            'id_fila' => $fila->id
        ]);
        $comentario2 = CcAmFilaComentario::factory()->create([
            'fecha' => Carbon::now()->addDays(2),
            'id_fila' => $fila->id
        ]);
        $enviar = [
            'idFila' => $fila->id, '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.listar-comentarios',
            $enviar
        ));
        $response->assertJson([
            ['id' => $comentario2->id, 'comentario' => $comentario2->comentario],
            ['id' => $comentario1->id, 'comentario' => $comentario1->comentario],
        ]);
    }

    public function test_puede_registrar_comentario()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $enviar = [
            'idFila' => $fila->id, 'comentario' => '', '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.registrar-comentario',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        $enviar['comentario'] = 'CONTENIDO';
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.registrar-comentario',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
    }

    public function test_puede_obtener_historial_precios()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $proveedor = Proveedor::factory()->create();
        $enviar = [
            'idFila' => $fila->id, 'proveedor' => $proveedor->id,
            'precio' => 100.5, 'moneda' => 'd', 'plazo' => 5, 'flete' => 10,
            'comentario' => 'COMENTARIO TEST', 'fondo' => null, '_token' => csrf_token()
        ];
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $responseProveedor = json_decode($this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.agregar-proveedor-fila',
            $enviar
        ))->getContent());

        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.obtener-historial-precios',
            ['idFila' => $responseProveedor->proveedor->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            ['tabla' => 'ccAm', 'precio' => '100.50']
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo-proveedor',
            ['idFila' => $responseProveedor->proveedor->id, 'campo' => 'precio', 'valor' => 105, '_token' => csrf_token()]
        ));
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.obtener-historial-precios',
            ['idFila' => $responseProveedor->proveedor->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            ['tabla' => 'ccAm', 'precio' => '105.00'],
            ['tabla' => 'ccAm', 'precio' => '100.50']
        ]);
    }

    public function test_puede_actualizar_datos_proveedor_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmProveedor::factory()->create();
        $enviar = ['idFila' => $fila->id, 'campo' => 'precio', 'valor' => 105, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo-proveedor',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo-proveedor',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
    }

    public function test_puede_obtener_proveedores_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $proveedor1 = CcAmProveedor::factory()->create(['id_fila' => $fila->id]);
        $proveedor2 = CcAmProveedor::factory()->create(['id_fila' => $fila->id]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.obtener-proveedores-fila',
            ['idFila' => $fila->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            'proveedores' => [
                ['id' => $proveedor2->id, 'moneda' => $proveedor2->moneda],
                ['id' => $proveedor1->id, 'moneda' => $proveedor1->moneda]
            ],
        ]);
    }

    public function test_puede_seleccionar_mejor_precio()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $proveedor1 = CcAmProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 100, 'flete' => 10]);
        $proveedor2 = CcAmProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 90, 'flete' => 10]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.seleccionar-mejor-precio',
            ['idFila' => $fila->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            'tipo' => 'success',
            'id' => $proveedor2->id
        ]);
        $cuadro = $fila->cuadroAm->cuadroCosto;
        $cuadro->estado_aprobacion = 4;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.seleccionar-mejor-precio',
            ['idFila' => $fila->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            'tipo' => 'error'
        ]);
    }

    public function test_puede_seleccionar_proveedor_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $proveedor1 = CcAmProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 100, 'flete' => 10]);
        $proveedor2 = CcAmProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 90, 'flete' => 10]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $enviar = ['idFila' => $fila->id, 'idFilaProveedor' => $proveedor2->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.seleccionar-proveedor-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success'
        ]);
        $fila->refresh();
        $this->assertTrue($fila->proveedor_seleccionado == $proveedor2->id);

        $cuadro = $fila->cuadroAm->cuadroCosto;
        $cuadro->estado_aprobacion = 4;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.seleccionar-proveedor-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error'
        ]);
    }

    public function test_puede_eliminar_proveedor_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $proveedor1 = CcAmProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 100, 'flete' => 10]);
        $fila->proveedor_seleccionado = $proveedor1->id;
        $fila->save();
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $enviar = ['idFilaProveedor' => $proveedor1->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.eliminar-proveedor-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success'
        ]);
        $fila->refresh();
        $this->assertTrue($fila->proveedor_seleccionado == null);
    }

    public function test_puede_agregar_proveedor_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $proveedor = Proveedor::factory()->create();
        $enviar = [
            'idFila' => $fila->id, 'proveedor' => $proveedor->id,
            'precio' => 100.5, 'moneda' => 'd', 'plazo' => 5, 'flete' => 10,
            'comentario' => 'COMENTARIO TEST', 'fondo' => null, '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.agregar-proveedor-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.agregar-proveedor-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
    }

    public function test_puede_agregar_proveedor_fila_con_fondo()
    {
        Session::start();
        $user = User::factory()->create();
        $fondo = FondoIngreso::factory()->create(['cantidad' => 10]);
        $fila = CcAmFila::factory()->create(['cantidad' => 20]);
        $proveedor = Proveedor::factory()->create();
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $enviar = [
            'idFila' => $fila->id, 'proveedor' => $proveedor->id,
            'precio' => 100.5, 'moneda' => 'd', 'plazo' => 5, 'flete' => 10,
            'comentario' => 'COMENTARIO TEST', 'fondo' => $fondo->id_fondo_proveedor, '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.agregar-proveedor-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'danger',
        ]);
    }

    public function test_puede_buscar_nro_parte()
    {
        Session::start();
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['part_no' => 'PARTTESTOKC']);
        $fila = CcAmFila::factory()->create();
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $enviar = ['idFila' => $fila->id, 'criterio' => 'PARTTESTOKC', '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.buscar-nro-parte',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        $fila->refresh();
        $this->assertTrue($fila->descripcion == $producto->descripcion);
    }

    public function test_montos_son_correctos()
    {
    }

    public function test_puede_actualizar_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $enviar = ['id' => $fila->id, 'campo' => 'descripcion', 'valor' => 'TEST OKC', '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        //Cuadro en sólo lectura
        $cuadro = $fila->cuadroAm->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
    }

    public function test_puede_actualizar_comprado_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create(['comprado' => false]);
        $enviar = ['idFila' => $fila->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-compra-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-compra-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        //Cuadro en sólo lectura
        $cuadro = $fila->cuadroAm->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-compra-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
    }

    public function test_puede_actualizar_fecha_entrega()
    {
        Session::start();
        $user = User::factory()->create();
        $ccAm = CcAm::factory()->create();
        $enviar = ['id' => $ccAm->id_cc, 'campo' => 'fecha_entrega', 'valor' => '10-05-2020', '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        $cuadro = $ccAm->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.actualizar-campo',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
    }

    public function test_puede_agregar_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $ccAm = CcAm::factory()->create();
        $enviar = ['idCuadro' => $ccAm->id_cc, '_token' => csrf_token()];

        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.agregar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.agregar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        //Cuadro en sólo lectura
        $cuadro = $ccAm->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.agregar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
    }

    public function test_puede_eliminar_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcAmFila::factory()->create();
        $enviar = ['idFila' => $fila->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.eliminar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        //Cuadro en sólo lectura
        $cuadro = $fila->cuadroAm->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.eliminar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        //Cuadro editable
        $cuadro->estado_aprobacion = 1;
        $cuadro->save();

        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccam.eliminar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
    }
}
