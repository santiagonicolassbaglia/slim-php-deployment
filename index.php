<?php
error_reporting(-1);
ini_set('display_errors', 1);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . "./vendor/autoload.php";

require_once './db/AccesoDatos.php';
 

require_once './controllers/EmpleadosController.php';
require_once './controllers/UsuarioController.php';

// Load ENV
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);





// Routes
$app->post("/usuario", \UsuarioController::class. ":CargarUno");
$app->post("/empleados", \EmpleadosController::class. ":CargarUno");
$app->run();
