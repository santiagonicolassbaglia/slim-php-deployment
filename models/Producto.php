<?php

class Producto
{
    public $id;
    public $idTipoEmpleado;
    public $tipoEmpleado;
    public $descripcion;
    public $precio;
    public $minutosPreparacion;

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        P.Id as id,
        P.IdTipoEmpleado as idTipoEmpleado,
        TE.Tipo as tipoEmpleado,
        P.Descripcion as descripcion,
        P.Precio as precio,
        P.MinutosPreparacion as minutosPreparacion
        FROM productos P
        INNER JOIN tipoempleado TE ON TE.Id = P.IdTipoEmpleado");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function ObtenerPorId($idProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        P.Id as id,
        P.IdTipoEmpleado as idTipoEmpleado,
        TE.Tipo as tipoEmpleado,
        P.Descripcion as descripcion,
        P.Precio as precio,
        P.MinutosPreparacion as minutosPreparacion
        FROM productos P
        INNER JOIN tipoempleado TE ON TE.Id = P.IdTipoEmpleado
        WHERE P.Id = :idProducto");
        $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function ObtenerProductosPorIds($arrayIds)
    {
        $listaProductos = array();
        
        foreach ($arrayIds as $key => $idProducto) 
        {
            $producto = Producto::ObtenerPorId($idProducto);
            array_push($listaProductos, $producto);
        }

        return $listaProductos;
    }

    public function CrearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO 
        productos (IdTipoEmpleado, Descripcion, Precio, MinutosPreparacion) 
        VALUES (:idTipoEmpleado, :descripcion, :precio, :minutosPreparacion)");

        $consulta->bindValue(':idTipoEmpleado', $this->idTipoEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':minutosPreparacion', $this->minutosPreparacion, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function CalcularMinutosTotalesPreparacion($listaProductos)
    {
        $minutosTotales = 0;

        foreach ($listaProductos as $key => $producto) 
        {
            $minutosTotales = $producto->minutosPreparacion > $minutosTotales ? $producto->minutosPreparacion : $minutosTotales;
        }

        return $minutosTotales;
    }

    public  function modificarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET
        IdTipoEmpleado = :idTipoEmpleado,
        Descripcion = :descripcion,
        Precio = :precio,
        MinutosPreparacion = :minutosPreparacion
        WHERE Id = :id");

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idTipoEmpleado', $this->idTipoEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':minutosPreparacion', $this->minutosPreparacion, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDato->obtenerUltimoId();




    }
    
    public static function borrarProducto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM productos WHERE Id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

   



    public static function CargarDesdeCSV($rutaArchivo)
    {
        $filas = array();
    
        try {
            $archivo = fopen($rutaArchivo, 'r');
            
            if (!$archivo) {
                throw new Exception("Error al abrir el archivo CSV.");
            }
            
             
            while (($datos = fgetcsv($archivo)) !== false) {
                $fila = array_map('trim', $datos);
                
                
                if (count($fila) != 4) {
                    throw new Exception("Error en el formato del archivo CSV.");
                }
            $producto = new Producto();
            $producto->idTipoEmpleado = $datos[0];
            $producto->descripcion = $datos[1];
            $producto->precio = $datos[2];
            $producto->minutosPreparacion = $datos[3];

            $filas[] = $producto;
            }
    
            fclose($archivo);
        } catch (Exception $ex) {
        
            $mensaje = "Error al cargar los datos desde el archivo CSV: " . $ex->getMessage();
            throw new Exception($mensaje);
        }
        
        return $filas;
    }
    
    
    function generarArchivoCSV($pro, $rutaArchivo)
    {
         // Abrir el archivo en modo escritura
    $archivo = fopen($rutaArchivo, "wb");


    $encabezados = array('ID Tipo Empleado', 'Descripcion', 'Precio', 'Minutos Preparacion');
    fputcsv($archivo, $encabezados);


    foreach ($pro as $producto) {
        $datos = array($pro->id, $pro->nombre, $pro->apellido, $pro->email);
        fputcsv($archivo, $datos);
    }

    // Cerrar el archivo
    fclose($archivo);
       
    }


    public static function ObtenerPorDescripcion($descripcion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        P.Id as id,
        P.IdTipoEmpleado as idTipoEmpleado,
        TE.Tipo as tipoEmpleado,
        P.Descripcion as descripcion,
        P.Precio as precio,
        P.MinutosPreparacion as minutosPreparacion
        FROM productos P
        INNER JOIN tipoempleado TE ON TE.Id = P.IdTipoEmpleado
        WHERE P.Descripcion = :descripcion");
        $consulta->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }
    public static function ToString($producto)
    {
        return "|Id: " . $producto->id . "| Descripcion: " . $producto->descripcion . "| Precio: " . $producto->precio;
    }

    public static function Validar($descripcion)
    {
        $mensajesError = array();

        if(empty(str_replace(' ', '', $descripcion)))
        {
            array_push($mensajesError, "Descripcion invalida.");
        }

        if(Producto::ObtenerPorDescripcion($descripcion) != null && count($mensajesError) < 1)
        {
            array_push($mensajesError, "El producto". $descripcion . " ya existe.");
        }

        return $mensajesError;
    }
}