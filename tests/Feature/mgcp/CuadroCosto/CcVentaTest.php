<?php

namespace Tests\Feature\mgcp\CuadroCosto;

use App\Http\Controllers\mgcp\CuadroCosto\CuadroCostoController;
use App\Models\mgcp\AcuerdoMarco\Producto\Producto;
use App\Models\mgcp\CuadroCosto\Ajuste\FondoIngreso;
use App\Models\mgcp\CuadroCosto\CcVenta;
use App\Models\mgcp\CuadroCosto\CcVentaFila;
use App\Models\mgcp\CuadroCosto\CcVentaFilaComentario;
use App\Models\mgcp\CuadroCosto\CcVentaProveedor;
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

class CcVentaTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() : void
    {
        parent::setUp();
        $this->markTestSkipped('Migrar de CCVenta a CCAM');
    }

    public function test_puede_obtener_comentarios()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcVentaFila::factory()->create();
        $comentario1 = CcVentaFilaComentario::factory()->create([
            'fecha' => Carbon::now()->addDays(1),
            'id_fila' => $fila->id
        ]);
        $comentario2 = CcVentaFilaComentario::factory()->create([
            'fecha' => Carbon::now()->addDays(2),
            'id_fila' => $fila->id
        ]);
        $enviar = [
            'idFila' => $fila->id, '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.listar-comentarios',
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
        $fila = CcVentaFila::factory()->create();
        $enviar = [
            'idFila' => $fila->id, 'comentario' => '', '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.registrar-comentario',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        $enviar['comentario'] = 'CONTENIDO';
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.registrar-comentario',
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
        $fila = CcVentaFila::factory()->create();
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
            'mgcp.cuadro-costos.ccventa.agregar-proveedor-fila',
            $enviar
        ))->getContent());

        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.obtener-historial-precios',
            ['idFila' => $responseProveedor->proveedor->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            ['tabla' => 'ccVenta', 'precio' => '100.50']
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-campo-proveedor',
            ['idFila' => $responseProveedor->proveedor->id, 'campo' => 'precio', 'valor' => 105, '_token' => csrf_token()]
        ));
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.obtener-historial-precios',
            ['idFila' => $responseProveedor->proveedor->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            ['tabla' => 'ccVenta', 'precio' => '105.00'],
            ['tabla' => 'ccVenta', 'precio' => '100.50']
        ]);
    }

    public function test_puede_actualizar_datos_proveedor_fila()
    {
        Session::start();
        $user = User::factory()->create();
        $fila = CcVentaProveedor::factory()->create();
        $enviar = ['idFila' => $fila->id, 'campo' => 'precio', 'valor' => 105, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-campo-proveedor',
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
            'mgcp.cuadro-costos.ccventa.actualizar-campo-proveedor',
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
        $fila = CcVentaFila::factory()->create();
        $proveedor1 = CcVentaProveedor::factory()->create(['id_fila' => $fila->id]);
        $proveedor2 = CcVentaProveedor::factory()->create(['id_fila' => $fila->id]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.obtener-proveedores-fila',
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
        $fila = CcVentaFila::factory()->create();
        $proveedor1 = CcVentaProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 100, 'flete' => 10]);
        $proveedor2 = CcVentaProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 90, 'flete' => 10]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.seleccionar-mejor-precio',
            ['idFila' => $fila->id, '_token' => csrf_token()]
        ));
        $response->assertJson([
            'tipo' => 'success',
            'id' => $proveedor2->id
        ]);
        $cuadro = $fila->cuadroVenta->cuadroCosto;
        $cuadro->estado_aprobacion = 4;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.seleccionar-mejor-precio',
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
        $fila = CcVentaFila::factory()->create();
        $proveedor1 = CcVentaProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 100, 'flete' => 10]);
        $proveedor2 = CcVentaProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 90, 'flete' => 10]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $enviar = ['idFila' => $fila->id, 'idFilaProveedor' => $proveedor2->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.seleccionar-proveedor-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success'
        ]);
        $fila->refresh();
        $this->assertTrue($fila->proveedor_seleccionado == $proveedor2->id);

        $cuadro = $fila->cuadroVenta->cuadroCosto;
        $cuadro->estado_aprobacion = 4;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.seleccionar-proveedor-fila',
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
        $fila = CcVentaFila::factory()->create();
        $proveedor1 = CcVentaProveedor::factory()->create(['id_fila' => $fila->id, 'precio' => 100, 'flete' => 10]);
        $fila->proveedor_seleccionado = $proveedor1->id;
        $fila->save();
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $enviar = ['idFilaProveedor' => $proveedor1->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.eliminar-proveedor-fila',
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
        $fila = CcVentaFila::factory()->create();
        $proveedor = Proveedor::factory()->create();
        $enviar = [
            'idFila' => $fila->id, 'proveedor' => $proveedor->id,
            'precio' => 100.5, 'moneda' => 'd', 'plazo' => 5, 'flete' => 10,
            'comentario' => 'COMENTARIO TEST', 'fondo' => null, '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.agregar-proveedor-fila',
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
            'mgcp.cuadro-costos.ccventa.agregar-proveedor-fila',
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
        $fondo=FondoIngreso::factory()->create(['cantidad'=>10]);
        $fila = CcVentaFila::factory()->create(['cantidad'=>20]);
        $proveedor=Proveedor::factory()->create();
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
            'mgcp.cuadro-costos.ccventa.agregar-proveedor-fila',
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
        $fila = CcVentaFila::factory()->create();
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 28
        ]);
        $enviar = ['idFila' => $fila->id, 'criterio' => 'PARTTESTOKC', '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.buscar-nro-parte',
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
        $fila = CcVentaFila::factory()->create();
        $enviar = ['id' => $fila->id, 'campo' => 'descripcion', 'valor' => 'TEST OKC', '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-campo-fila',
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
            'mgcp.cuadro-costos.ccventa.actualizar-campo-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        //Cuadro en sólo lectura
        $cuadro = $fila->cuadroVenta->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-campo-fila',
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
        $fila = CcVentaFila::factory()->create(['comprado' => false]);
        $enviar = ['idFila' => $fila->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-compra-fila',
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
            'mgcp.cuadro-costos.ccventa.actualizar-compra-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        //Cuadro en sólo lectura
        $cuadro = $fila->cuadroVenta->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-compra-fila',
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
        $ccVenta = CcVenta::factory()->create();
        $enviar = ['id' => $ccVenta->id_cc, 'campo' => 'fecha_entrega', 'valor' => '10-05-2020', '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-campo',
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
            'mgcp.cuadro-costos.ccventa.actualizar-campo',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        $cuadro = $ccVenta->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.actualizar-campo',
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
        $ccVenta = CcVenta::factory()->create();
        $enviar = ['idCuadro' => $ccVenta->id_cc, '_token' => csrf_token()];

        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.agregar-fila',
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
            'mgcp.cuadro-costos.ccventa.agregar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
        //Cuadro en sólo lectura
        $cuadro = $ccVenta->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.agregar-fila',
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
        $fila = CcVentaFila::factory()->create();
        $enviar = ['idFila' => $fila->id, '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.eliminar-fila',
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
        $cuadro = $fila->cuadroVenta->cuadroCosto;
        $cuadro->estado_aprobacion = 2;
        $cuadro->save();
        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.eliminar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'error',
        ]);
        //Cuadro editable
        $cuadro->estado_aprobacion = 1;
        $cuadro->save();

        $response = $this->actingAs($user)->post(route(
            'mgcp.cuadro-costos.ccventa.eliminar-fila',
            $enviar
        ));
        $response->assertJson([
            'tipo' => 'success',
        ]);
    }
}
