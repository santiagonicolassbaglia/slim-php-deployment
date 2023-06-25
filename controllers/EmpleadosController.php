<?php
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';
require_once './db/AccesoDatos.php';
 

 

 

class EmpleadosController extends Empleado 
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
        $payload = json_encode(array("error" => "Falta el campo: " . implode(', ', $missingParams)));
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

    $payload = json_encode(array("mensaje" => "El Empleado ah sido creado", "id" => $nuevoId));

    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json');
}




public function TraerUno($request, $response, $args)
{
    $usuario = $args['usuario'];
    $payload = null;

    try {
        if (empty(str_replace(' ', '', $usuario))) {
            throw new Exception("El usuario ingresado es inválido.");
        }

        $empleado = Empleado::Obtenerusuario($usuario);

        if (!$empleado) {
            throw new Exception("No se encontró un empleado con el usuario " . $usuario);
        }

        $payload = json_encode($empleado);
    } catch (Exception $ex) {
        $mensaje = $ex->getMessage();
        $payload = json_encode(array('error' => $mensaje));
    } finally {
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

public function TraerTodos($request, $response, $args)
{
    $lista = Empleado::ObtenerTodos();
    $payload = null;

    try {
        if (empty($lista)) {
            throw new Exception("No se encontraron empleados.");
        }

        $payload = json_encode(array("listaEmpleado" => $lista));
    } catch (Exception $ex) {
        $mensaje = $ex->getMessage();
        $payload = json_encode(array('error' => $mensaje));
    } finally {
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}



        public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $id = $parametros['id'];
      $idTipoEmpleado = $parametros['idTipoEmpleado'];
      $idEstado = $parametros['idEstado'];
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];

      $payload = null;

      try 
      {
        $erroresValidacion = Empleado::Validar($usuario);

        if(count($erroresValidacion) > 0)
        {
          throw new Exception(json_encode($erroresValidacion), 800);
        }

        $empleado = new Empleado();
        $empleado->id = $id;
        $empleado->idTipoEmpleado = $idTipoEmpleado;
        $empleado->idEstado = $idEstado;
        $empleado->usuario = $usuario;
        $empleado->clave = $clave;
        $empleado->ModificarEmpleado($empleado);

        $payload = json_encode(array("mensaje" => "Empleado modificado con exito"));
      } 
      catch (Exception $ex) 
      {
        $mensaje = $ex->getMessage();

         
        $payload = json_encode(array('Error' => $mensaje));
      }
      finally
      {
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
      }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


 
    public function  CargarCSV($request, $response, $args)
    {
      $payload = null;

      try 
      {
        $csv = $_FILES['csv'];
        $lista = Empleado::CargarDesdeCSV($csv["tmp_name"]);
        $errores = null;
  
        foreach ($lista as $key => $empleado) 
        {
          $errores = Empleado::Validar($empleado->usuario);
        }
  
        if(count($errores) > 0)
        {
          throw new Exception(json_encode($errores), 800);
        }
  
        foreach ($lista as $key => $empleado) 
        {
          $nuevoId = $empleado->CrearEmpleado();
          $empleado->id = $nuevoId;
        }
  
        $payload = json_encode(array("listaEmpleados" => $lista));
      } 
      catch (Exception $ex) 
      {
          $mensaje = $ex->getMessage();

          if($ex->getCode() == 800)
          {
              $mensaje = json_decode($ex->getMessage());
          }

          $payload = json_encode(array('Error' => $mensaje));
      }
      finally
      {
          $response->getBody()->write($payload);
          return $response->withHeader('Content-Type', 'application/json');
      }
    }

    public function generarCSV($request, $response, $args)
    { 
         
            $empleados = Empleado::ObtenerTodos();

          
            $rutaArchivo = "./empleados.csv";
            $this->generarArchivoCSV($empleados, $rutaArchivo );

            // Descargar el archivo CSV
            $fileResponse = $response->withHeader('Content-Type', 'text/csv')
                ->withHeader('Content-Disposition', 'attachment; filename="empleados.csv"')
                ->withHeader('Pragma', 'no-cache')
                ->withHeader('Expires', '0')
                ->withHeader('Content-Length', filesize($rutaArchivo));

            readfile($rutaArchivo);
            unlink($rutaArchivo);

            return $fileResponse;
         
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








 