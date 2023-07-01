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
require __DIR__ . "./vendor/autoload.php";
require_once './middlewares/AutentificadorJWT.php';

require_once './controllers/LoguerController.php';
require_once './controllers/EmpleadosController.php';
require_once './controllers/ProductosController.php';
require_once './controllers/MesasController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/FacturaController.php';

require_once './controllers/EncuestaController.php';
require_once './middlewares/ConToken.php';
require_once './middlewares/SoloAdmin.php';

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
  $group->post('/altaEmpleado', \EmpleadosController::class . ':CargarUno');
  // ->add(new SoloAdmin());
  $group->put('/', \EmpleadosController::class . ':ModificarUno')->add(new SoloAdmin());
  $group->get('/listarEmpleados', \EmpleadosController::class . ':TraerTodos');
  $group->delete('/', \EmpleadosController::class . ':BorrarUno')->add(new SoloAdmin());
  $group->get('/csv/', \EmpleadosController::class . ':ExportarEmpleado');
});
// ->add(new ConToken());

$app->group('/productos', function (RouteCollectorProxy $group) {
$group->post('/altaProducto', \ProductosController::class . ':CargarProducto');
$group->get('/exportarCSV', \ProductosController::class . ':ExportarProductos')->add(new SoloAdmin());
$group->get('/listarProductos', \ProductosController::class . ':MostrarProductos');
$group->post('/importarCSV', \ProductosController::class . ':ImportarProductos')->add(new SoloAdmin());
})->add(new ConToken());

$app->group('/mesas', function (RouteCollectorProxy $group) {
$group->post('/altaMesa', \MesasController::class . ':CargarMesa');
$group->get('/listarMesas', \MesasController::class . ':MostrarMesas')->add(new SoloAdmin());
$group->put('/abrirMesa', \MesasController::class . ':AbrirMesa')->add(new SoloAdmin());
$group->put('/cambiarEstado', \MesasController::class . ':CambiarEstadoMesa')->add(new SoloAdmin());
$group->delete('/cerrarMesa', \MesasController::class . ':CerrarMesa')->add(new SoloAdmin());
})->add(new ConToken());

$app->group('/pedidos', function (RouteCollectorProxy $group) { 
$group->post('/altaPedido', \PedidoController::class . ':CargarPedido')->add(new SoloAdmin());
$group->get('/listarPedidos', \PedidoController::class . ':MostrarPedidos')->add(new SoloAdmin());
$group->get('/MostrarPedidosEmpleado', \PedidoController::class . ':MostrarPedidosEmpleado');
$group->get('/ConsultarPedidosListos', \PedidoController::class . ':ConsultarPedidosListos')->add(new SoloAdmin());
$group->get('/MostrarPedidosPreparados', \PedidoController::class . ':MostrarPedidosPreparados');
$group->get('/MesaPopular',  \PedidoController::class . ':ConsultarMesaPopular')->add(new SoloAdmin());
$group->put('/prepararPedido', \PedidoController::class . ':PrepararPedido');
$group->put('/PedidoListo', \PedidoController::class . ':CambiarEstadoListo');
})->add(new ConToken());

$app->post('/Encuesta', \EncuestaController::class . ':CargarEncuesta');
$app->post('/Facturar', \FacturaController::class . ':CargarFactura')->add(new SoloAdmin());
$app->get('/MostrarFacturas', \FacturaController::class . ':MostrarFacturas');
$app->get('/MejoresEncuestas', \EncuestaController::class . ':MostrarMejores')->add(new SoloAdmin());
$app->post('/demoraPedido', \PedidoController::class . ':ConsultarDemoraPedido');
$app->post('/loguin', \LoguerController::class . ':GenerarToken');

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("TP Programacion III");
  return $response;
});

$app->run();
  