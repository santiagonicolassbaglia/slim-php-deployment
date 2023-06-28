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
require_once './middlewares/Logger.php';

require_once './controllers/EmpleadosController.php';
require_once './controllers/ProductosController.php';
require_once './controllers/MesasController.php';
require_once './controllers/PedidosController.php';
require_once './controllers/PedidoProductoController.php';
require_once './controllers/EncuestaController.php';


// Load ENV
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);





// JWT
$app->group('/autentificacion', function (RouteCollectorProxy $group) {

    $group->post('/crearToken', function (Request $request, Response $response) {    
      $parametros = $request->getParsedBody();
     
      $usuario = $parametros['usuario'];
      $contraseña = $parametros['clave'];
  
      $datos = array('usuario' => $usuario, 'clave' => $contraseña);
  
      try 
      {
        $token = AutentificadorJWT::CrearTokenEmpleado($datos);
        $payload = json_encode(array('usuario' => $usuario, 'jwt' => $token));
      } 
      catch (Exception $e) 
      {
        $payload = json_encode(array('error' => $e->getMessage()));
      }
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    });
  });

 

  
 
    


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
  $group->get('[/csv]', \EmpleadosController::class . ':GenerarCSV');
  $group->post('[/CargarCSV]', \EmpleadosController::class . ':CargarCSV');

  
  $group->get('[/]', \EmpleadosController::class . ':TraerTodos');
    $group->get('/csv', \EmpleadosController::class . ':ObtenerCSV');
    $group->get('/pdf', \EmpleadosController::class . ':ObtenerPDF');
    $group->get('/{usuario}', \EmpleadosController::class . ':TraerUno')->add(\Logger::class . ':VerificarCredenciales');
   
  });
   
  

  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductosController::class . ':TraerTodos')->add(\Logger::class . ':VerificarCredenciales');
    $group->get('/pdf', \ProductosController::class . ':ObtenerPDF');
    $group->get('/csv', \ProductosController::class . ':ObtenerCSV');
    $group->post('[/]', \ProductosController::class . ':CargarUno')->add(\Logger::class . ':VerificarCredenciales');
    $group->post('/cargar/csv', \ProductosController::class . ':CargarCSV');
  });
  
  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidosController::class . ':TraerTodos');
    $group->get('/{codigo}', \PedidosController::class . ':TraerUno');
    $group->post('[/]', \PedidosController::class . ':CargarUno');
    $group->post('/cobrar', \PedidosController::class . ':Cobrar');
    $group->post('/estado/cerrar', \PedidosController::class . ':CerrarPedidoMesa');
  })->add(\Logger::class . ':VerificarCredenciales');
  
  $app->group('/pedidos-productos', function (RouteCollectorProxy $group) {
    $group->get('/estado', \PedidoProductoController::class . ':ObtenerProductosListos');
    $group->get('/estado/empleado', \PedidoProductoController::class . ':ProductosPendientesEmpleado');
    $group->post('/estado/empleado', \PedidoProductoController::class . ':CambiarEstadoEmpleado');
  })->add(\Logger::class . ':VerificarCredenciales');
  
  $app->group('/encuesta', function (RouteCollectorProxy $group) {
    $group->post('[/]', \EncuestaController::class . ':AltaEncuesta');
    $group->get('[/]', \EncuestaController::class . ':MejoresEncuestas')->add(\Logger::class . ':VerificarCredenciales');
  });
  


  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->post('/estado', \MesasController::class . ':CambiarMesaServida');
    $group->post('[/]', \MesasController::class . ':CargarUno');
    $group->get('[/]', \MesasController::class . ':TraerTodos');
    $group->get('/disponibles', \MesasController::class . ':TraerDisponible');
    $group->get('/estadistica/usadas', \MesasController::class . ':ObtenerMesasMasUsadas');
   
   
  })->add(\Logger::class . ':VerificarCredenciales');
  

  $app->run();
  