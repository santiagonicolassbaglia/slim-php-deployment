<?php
require_once "./models/Producto.php";
require_once "./models/Archivos.php";

class ProductoController extends Producto
{
    public static $sectres = array("Vinoteca", "Cerveceria", "Cocina", "CandyBar");
    public function CargarProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $descripcion = $parametros['descripcion'];
        $precio = $parametros['precio'];
        $sector = $parametros['sector'];

        if(in_array($sector, $this::$sectres))
        {
            $producto = new Producto();
            $producto->descripcion = $descripcion;
            $producto->precio = $precio;
            $producto->sector = $sector;
            $producto->AltaProducto();
            $payload = json_encode(array("Mensaje" => "Producto creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "Sector de producto no valido. (Vinoteca / Cerveceria / Cocina / CandyBar)"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function MostrarProductos($request, $response, $args)
    {
        $lista = Producto::GetProductos();
        $payload = json_encode(array("Productos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ExportarProductos($request, $response, $args)
    {
        try
        {
            $archivo = Archivos::ExportarCSVProductos("./csv/empleado.csv");
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

    public function ImportarProductos($request, $response, $args)
    {
        try
        {
            $archivo = ($_FILES["archivo"]);
            Producto::LoadCSV($archivo["tmp_name"]);
            $payload = json_encode(array("Mensaje" => "Productos cargados!"));
        }
        catch(Throwable $mensaje)
        {
            $payload = json_encode(array("Error" => $mensaje->getMessage()));
        }
        finally
        {
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'text/csv');
        }    
       
    }
}

?>