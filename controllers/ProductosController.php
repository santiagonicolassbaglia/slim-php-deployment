<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
 

class ProductosController extends Producto
{
    public function TraerTodos($request, $response, $args)
    {
      $payload = null;

      try 
      {
        $lista = Producto::ObtenerTodos();

        if(count($lista) < 1)
        {
          throw new Exception("No se encontraron productos.");
        }

        $payload = json_encode(array("productos" => $lista));
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

    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $idTipoEmpleado = $parametros['idTipoEmpleado'];
      $descripcion = $parametros['descripcion'];
      $precio = $parametros['precio'];
      $minutosPreparacion = $parametros['minutosPreparacion'];

      $payload = null;

      try 
      {
        $erroresValidacion = Producto::Validar($descripcion);

        if(count($erroresValidacion) > 0)
        {
          throw new Exception(json_encode($erroresValidacion), 800);
        }

        $producto = new Producto();
        $producto->idTipoEmpleado = $idTipoEmpleado;
        $producto->descripcion = $descripcion;
        $producto->precio = $precio;
        $producto->minutosPreparacion = $minutosPreparacion;
        $nuevoId = $producto->CrearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito", "id" => $nuevoId));
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

    // public function ObtenerPDF($request, $response, $args)
    // {
    //   $productos = Producto::ObtenerTodos();

    //   if(count($productos) > 0)
    //   {
        
    //     $pdf = new FPDF();
    //     $pdf->AddPage();
    //     $pdf->SetFont('Arial','B',9);

    //     foreach ($productos as $key => $producto) 
    //     {
    //       $stringProducto = Producto::ToString($producto);
    //       $pdf->Cell(140,10, $stringProducto, 0, 1);
    //     }

    //     $pdf->Output('D', 'productosResto.pdf', true);

    //     return $response->withHeader('Content-Type', 'application/pdf');
    //   }
    // }

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

}