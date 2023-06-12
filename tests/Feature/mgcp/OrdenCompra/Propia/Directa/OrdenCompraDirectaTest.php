<?php

namespace Tests\Feature\mgcp\OrdenCompra\Propia\Directa;

use App\Models\mgcp\AcuerdoMarco\Empresa;
use App\Models\mgcp\AcuerdoMarco\Entidad\Entidad;
use App\Models\mgcp\OrdenCompra\Propia\Directa\ComentarioOcDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Directa\OrdenCompraDirecta;
use App\Models\mgcp\OrdenCompra\Propia\Etapa;
use App\Models\mgcp\OrdenCompra\Propia\OrdenCompraPropiaView;
use App\Models\mgcp\Usuario\RolUsuario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class OrdenCompraDirectaTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_puede_acceder_formulario_nueva()
    {
        Session::start();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('mgcp.ordenes-compra.propias.directas.nueva'));
        $response->assertSee("Su usuario no tiene permiso");
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 129
        ]);
        $response = $this->actingAs($user)->get(route('mgcp.ordenes-compra.propias.directas.nueva'));
        $response->assertSee("Campos requeridos");
    }

    public function test_puede_crear_oc()
    {
        Session::start();
        $user = User::factory()->create();
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 129
        ]);
        $enviar=[
            'empresa' => Empresa::first()->id,
            'cliente' => Entidad::factory()->create()->id,
            'lugar_entrega' => 'LUGARTESTOKCTEST',
            'monto_total' => '1000',
            'responsable' => User::factory()->create()->id,
            'fecha_entrega' => '01-01-2020',
            'fecha_publicacion' => '01-02-2020',
            'occ' => 'TESTOCC',
            'etapa'=>Etapa::first()->id,
            'archivos' => [new \Illuminate\Http\UploadedFile(storage_path('app/mgcp/pruebas/prueba.pdf'), 'prueba.pdf', null, null, true)],
            '_token' => csrf_token()
        ];
        $response = $this->actingAs($user)->post(route('mgcp.ordenes-compra.propias.directas.registrar'),$enviar);
        $this->assertDatabaseHas('mgcp_ordenes_compra.oc_directas',[
            'id_empresa'=>$enviar['empresa'],
            'id_entidad'=>$enviar['cliente'],
            'lugar_entrega'=>$enviar['lugar_entrega'],
            'id_corporativo'=>$enviar['responsable'],
        ]);
    }

    public function test_puede_actualizar_campos()
    {
        Session::start();
        $user = User::factory()->create();
        $orden=OrdenCompraDirecta::factory()->create();
        $enviar=[
            'id'=>$orden->id,
            'tipoOrden'=>'directa',
            'campo'=>'occ',
            'valor'=>'NUEVOVALOROCC',
            '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route('mgcp.ordenes-compra.propias.actualizar-campo'),$enviar);
        $response->assertJson([
            'tipo' => 'success',
        ]);
        $orden->refresh();
        $this->assertTrue($orden->occ==$enviar['valor']);
    }

    public function test_puede_obtener_informacion_adicional()
    {
        Session::start();
        $user = User::factory()->create();
        $orden=OrdenCompraDirecta::factory()->create();
        $enviar=[
            'id'=>$orden->id,
            'tipo'=>'directa',
            '_token' => csrf_token()];
        $response = $this->actingAs($user)->post(route('mgcp.ordenes-compra.propias.obtener-informacion-adicional'),$enviar);
        $response->assertJson([
            'tipo' => 'error',
        ]);
        RolUsuario::factory()->create([
            'id_usuario' => $user->id,
            'id_rol' => 31
        ]);
        $response = $this->actingAs($user)->post(route('mgcp.ordenes-compra.propias.obtener-informacion-adicional'),$enviar);
        $response->assertJson([
            'tipo' => 'success',
            'lugar_entrega'=>$orden->lugar_entrega
        ]);
    }

    public function test_tiene_comentarios()
    {
        $orden=OrdenCompraDirecta::factory()->create();
        $view=OrdenCompraPropiaView::where('id',$orden->id)->where('tipo','directa')->first();
        $this->assertTrue($view->tiene_comentarios==0); 
        ComentarioOcDirecta::factory()->create([
            'id_oc'=>$orden->id
        ]);
        $this->assertTrue($view->tiene_comentarios==1); 
    }

    public function test_ultimo_comentario()
    {
        $orden=OrdenCompraDirecta::factory()->create();
        $view=OrdenCompraPropiaView::where('id',$orden->id)->where('tipo','directa')->first();
        $this->assertTrue($view->tiene_comentarios==0); 
        $comentario1=ComentarioOcDirecta::factory()->create([
            'id_oc'=>$orden->id,
            'fecha'=>Carbon::now()->addDays(-1)
        ]);
        $comentario2=ComentarioOcDirecta::factory()->create([
            'id_oc'=>$orden->id
        ]);
        $comentario3=ComentarioOcDirecta::factory()->create([
            'id_oc'=>$orden->id,
            'fecha'=>Carbon::now()->addDays(-1)
        ]);
        $this->assertTrue($view->ultimoComentario()->comentario==$comentario2->comentario); 
    }
}
