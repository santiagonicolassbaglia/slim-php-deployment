<?php
error_reporting(-1);
ini_set('display_errors', 1);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;





require_once './db/AccesoDatos.php';
require __DIR__ . "/vendor/autoload.php";
require_once './middlewares/AutentificadorJWT.php';

require_once './controllers/LoguerController.php';
require_once './controllers/EmpleadosController.php';
require_once './controllers/ProductosController.php';
require_once './controllers/MesasController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/FacturaController.php';
require_once './controllers/csvControler.php';
require_once './controllers/EncuestaController.php';
require_once './middlewares/ConToken.php';
require_once './middlewares/SoloAdmin.php';
require_once './middlewares/Validaciones.php';
// Load ENV
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);





  
// Routes
// $app->post("/usuario", \UsuarioController::class. ":CargarUno");
// $app->post("/empleados", \EmpleadosController::class. ":CargarUno");
// $app->run();

// $app->group('/empleados', function (RouteCollectorProxy $group) {
//     $group->post('[/]', \EmpleadosController::class . ':CargarUno');

// });
//$app->run();



// API

 

$app->group('/empleados', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EmpleadosController::class . ':CargarUno');
  // ->add(new SoloAdmin());
  $group->put('/{id}', \EmpleadosController::class . ':ModificarUno');//falta
  // ->add(new SoloAdmin())
  $group->get('/traerEmpleados', \EmpleadosController::class . ':TraerTodos');
  $group->delete('/', \EmpleadosController::class . ':BorrarUno');//falta
  // ->add(new SoloAdmin());
  $group->get('/csv', \EmpleadosController::class . ':Exportar');
});
// ->add(new ConToken());

             
$app->group('/productos', function (RouteCollectorProxy $group) {
$group->post('[/]', \ProductosController::class . ':CargarProducto');
$group->get('/csv', \ProductosController::class . ':ExportarProductos');
// ->add(new SoloAdmin());
$group->get('[/]', \ProductosController::class . ':MostrarProductos');
$group->post('/importarCSV', \ProductosController::class . ':ImportarProductos');//falta
// ->add(new SoloAdmin());
});
// ->add(new ConToken())

$app->group('/mesas', function (RouteCollectorProxy $group) {
$group->post('[/]', \MesasController::class . ':CargarMesa');
$group->get('[/]', \MesasController::class . ':MostrarMesas');
// ->add(new SoloAdmin());
$group->put('/abrirMesa', \MesasController::class . ':AbrirMesa');// falta
// ->add(new SoloAdmin());
$group->put('/cambiarEstado', \MesasController::class . ':CambiarEstadoMesa');// falta
// ->add(new SoloAdmin());
$group->delete('/cerrarMesa', \MesasController::class . ':CerrarMesa');
// ->add(new SoloAdmin());
$group->put('/comiendo/{idMesa}', \MesasController::class . ':CambiarEstadoMesa');
$group->put('/llevarCuenta/{idMesa}', \MesasController::class . ':cobrar')
  ->add(\Validaciones::validarRoles(['Socio','Mozo']));

  $group->put('/cerrarMesa/{idMesa}', \MesasController::class . ':CerrarMesa')
  ->add(\Validaciones::validarRoles(['Socio']));
  
})->add(\Validaciones::validarRoles(['Socio', 'Mozo']))
->add(\Validaciones::class . ':validarJWTUsuario');

$app->group('/pedidos', function (RouteCollectorProxy $group) { 
$group->post('[/]', \PedidoController::class . ':CargarPedido')
->add(\Validaciones::validarRoles(['Socio', 'Mozo']))->add(\Validaciones::class . ':validarJWTUsuario');
 
$group->get('/listarPedidos', \PedidoController::class . ':MostrarPedidos')
->add(\Validaciones::validarRoles(['Socio']))->add(\Validaciones::class . ':validarJWTUsuario');
 
$group->get('/listarPedidosPendientes', \PedidoController::class . ':MostrarPedidosPendientes')
->add(\Validaciones::class . ':validarJWTUsuario');

$group->post('/prepararPedido', \PedidoController::class . ':PrepararPedido')
->add(\Validaciones::class . ':validarJWTUsuario');
 

$group->get('/{idMesa}/{codigoMesa}', \PedidoController::class . ':mostrarMesacodigo');

$group->get('/MostrarPedidosPreparacion', \PedidoController::class . ':MostrarPedidosPreparacion')
->add(\Validaciones::class . ':validarJWTUsuario');

$group->post('/paraServir', \PedidoController::class . ':paraServir')->add(\Validaciones::class . ':validarJWTUsuario');
$group->get('/MostrarPedidosParaServir', \PedidoController::class . ':MostrarPedidosParaServir')
->add(\Validaciones::validarRoles(['Socio', 'Mozo']))->add(\Validaciones::class . ':validarJWTUsuario');

$group->post('/servir', \PedidoController::class . ':servir')->add(\Validaciones::validarRoles(['Socio', 'Mozo']))->add(\Validaciones::class . ':validarJWTUsuario');








$group->get('/MostrarPedidosEmpleado', \PedidoController::class . ':MostrarPedidosEmpleado');
$group->get('/ConsultarPedidosListos', \PedidoController::class . ':ConsultarPedidosListos');//->add(\Validaciones::class . ':ValidarJWT') ;
$group->get('/MostrarPedidosPreparados', \PedidoController::class . ':MostrarPedidosPreparados');
$group->get('/MesaPopular',  \PedidoController::class . ':ConsultarMesaPopular');//->add(\Validaciones::class . ':ValidarJWT') ;
$group->put('/PedidoListo', \PedidoController::class . ':CambiarEstadoListo');
}) ;
 

$app->post('/Encuesta', \EncuestaController::class . ':CargarUno');
$app->post('/Facturar', \FacturaController::class . ':CargarFactura')->add(\Validaciones::class . ':ValidarJWT') ;
$app->get('/MostrarFacturas', \FacturaController::class . ':MostrarFacturas');
 
$app->get('/MejoresEncuestas', \EncuestaController::class . ':MejoresEncuestas')->add(\Validaciones::validarRoles(['Socio']))->add(\Validaciones::class . ':validarJWTUsuario') ;
$app->post('/demoraPedido', \PedidoController::class . ':ConsultarDemoraPedido');
$app->post('/loguin', \LoguerController::class . ':GenerarToken');

$app->post('/csv/cargar/{baseDatos}', \csvControler::class . ':cargarcsv');
$app->get('/archivos/descargar/{baseDatos}',  \csvControler::class . ':descargarcsv');
$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("TP Programacion III");
  return $response;
});

$app->run();
  