<?php
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/AutentificadorJWT.php';

 

 

class EmpleadosController extends Empleado 
{
    public static $roles = array("Pastelero","Bartender", "Cocinero", "Cervecero", "Mozo", "Socio");

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

    $requiredParams = ['nombre', 'clave', 'rol' ];

    $missingParams = [];
    foreach ($requiredParams as $param) {
        if (!isset($parametros[$param])) {
            $missingParams[] = $param;
        }
    }

    if (!empty($missingParams)) {
        $payload = json_encode(array("error" => "Falta el campo: " . implode(', ', $missingParams)));
        $response->getBody()->write($payload);
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }

    $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];
        $claveHasheada = md5($clave);
        $rol = $parametros['rol'];

        if(in_array($rol, $this::$roles))
        {
            $empleado = new Empleado();
            $empleado->nombre = $nombre;
            $empleado->clave = $claveHasheada;
            $empleado->rol = $rol;
            $empleado->altaEmpleado();
            $payload = json_encode(array("Mensaje" => "Usuario creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "El Rol es inexistente."));
        }



    //  //CrearToken
    //   $datos = array('usuario' => $usuario, 'clave' => $clave);
    //   $token = AutentificadorJWT::CrearTokenEmpleado($datos);
    //   $payload = json_encode(array('usuario' => $usuario, 'jwt' => $token));
    //   $response->getBody()->write($payload);
   


    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json');
}




public function TraerUnoPorId($request, $response, $args)
{
    $id = $args['id'];
    $empleado = Empleado::getEmpleadoPorId($id);
    $payload = json_encode($empleado);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');   
 
}

public function TraerTodos($request, $response, $args)
{
    $lista = Empleado::GetEmpleados();
        $payload = json_encode(array("Empleados" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
}



        public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        Empleado::UpdateEmpleado($id);
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuarioId = $parametros['id'];
        Empleado::DeleteEmpleado($usuarioId);
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ExportarArma($request, $response, $args)
    {
        try
        {
            $archivo = Archivos::ExportarCSVEmpleado("./csv/empleado.csv"); 
            if(file_exists($archivo) && filesize($archivo) > 0)
            {
                $payload = json_encode(array("Archivo creado:" => $archivo));
            }
            else
            {
                $payload = json_encode(array("Error" => "Datos ingresados invalidos."));
            }
            $response->getBody()->write($payload);
        }
        catch(Exception $e)
        {
            echo $e;
        }
        finally
        {
            return $response->withHeader('Content-Type', 'text/csv');
        }    
    }
 
 
    
    // public function ObtenerPDF($request, $response, $args)
    // {
    //     $lista = Empleado::ObtenerTodos();

    //     if (count($lista) > 0) {
    //         // Crea una nueva instancia de TCPDF
    //         $pdf = new TCPDF();

    //         // Establece la orientación y el tamaño de página
    //         $pdf->setPrintHeader(false);
    //         $pdf->setPrintFooter(false);
    //         $pdf->AddPage();

    //         // Define el contenido del PDF
    //         $contenido = 'Lista de empleados:' . PHP_EOL;
    //         foreach ($lista as $empleado) {
    //             $contenido .= Empleado::ToString($empleado) . PHP_EOL;
    //         }

    //         // Agrega el contenido al PDF
    //         $pdf->writeHTML($contenido, true, false, true, false, '');

    //         // Genera el PDF y guarda en un archivo
    //         $pdf->Output('empleados.pdf', 'D');

    //         return $response->withHeader('Content-Type', 'application/pdf');
    //     }
    // }
    
 
    //generar archivo .pdf

    // public function generarPDF($request, $response, $args)
    // {
    //     $lista = Empleado::ObtenerTodos();

    //     if (count($lista) > 0) {
    //         // Crea una nueva instancia de TCPDF
    //         $pdf = new TCPDF();

    //         // Establece la orientación y el tamaño de página
    //         $pdf->setPrintHeader(false);
    //         $pdf->setPrintFooter(false);
    //         $pdf->AddPage();

    //         // Define el contenido del PDF
    //         $contenido = 'Lista de empleados:' . PHP_EOL;
    //         foreach ($lista as $empleado) {
    //             $contenido .= Empleado::ToString($empleado) . PHP_EOL;
    //         }

    //         // Agrega el contenido al PDF
    //         $pdf->writeHTML($contenido, true, false, true, false, '');

    //         // Genera el PDF y guarda en un archivo
    //         $pdf->Output('empleados.pdf', 'D');

    //         return $response->withHeader('Content-Type', 'application/pdf');
    //     }
    // }
    


}








 