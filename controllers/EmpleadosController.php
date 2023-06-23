<?php
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
require_once './db/AccesoDatos.php';

class EmpleadosController extends Empleado implements IApiUsable
{


    // public function CargarUno($request, $response, $args)
    // {
    //     $parametros = $request->getBody();

    //     $idTipoEmpleado = $parametros['idTipoEmpleado'];
    //     $idEstado = $parametros['idEstado'];
    //     $usuario = $parametros['usuario'];
    //     $clave = $parametros['clave'];

    //     $empleado = new Empleado();
    //     $empleado->idTipoEmpleado = $idTipoEmpleado;
    //     $empleado->idEstado = $idEstado;
    //     $empleado->usuario = $usuario;
    //     $empleado->clave = $clave;

    //     $nuevoId = $empleado->CrearEmpleado();

    //     $payload = json_encode(array("mensaje" => "Empleado creado con exito", "id" => $nuevoId));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }


  public function CargarUno($request, $response, $args)
{
    $parametros = $request->getParsedBody();

    $requiredParams = ['idTipoEmpleado', 'idEstado', 'usuario', 'clave'];

    $missingParams = [];
    foreach ($requiredParams as $param) {
        if (!isset($parametros[$param])) {
            $missingParams[] = $param;
        }
    }

    if (!empty($missingParams)) {
        $payload = json_encode(array("error" => "Falta el parámetro: " . implode(', ', $missingParams)));
        $response->getBody()->write($payload);
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }

    $idTipoEmpleado = $parametros['idTipoEmpleado'];
    $idEstado = $parametros['idEstado'];
    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];

    $empleado = new Empleado();
    $empleado->idTipoEmpleado = $idTipoEmpleado;
    $empleado->idEstado = $idEstado;
    $empleado->usuario = $usuario;
    $empleado->clave = $clave;

    $nuevoId = $empleado->CrearEmpleado();

    $payload = json_encode(array("mensaje" => "Empleado creado con éxito", "id" => $nuevoId));

    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json');
}
    public function TraerUno($request, $response, $args)
    {
        $usuario = $args['usuario'];
        $empleado = Empleado::ObtenerUsuario($usuario);
        $payload = json_encode($empleado);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Empleado::ObtenerTodos();
        $payload = json_encode(array("listaEmpleado" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();

        // $usuario = $parametros['usuario'];
        // Usuario::modificarUsuario($idUsuario,$usuario,$clave);

        // $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        // $response->getBody()->write($payload);
        // return $response
        //   ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
