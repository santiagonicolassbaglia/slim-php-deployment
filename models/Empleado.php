<?php

class Empleado
{
    public $id;
    public $idTipoEmpleado;
    public $tipoEmpleado;
    public $idEstado;
    public $estado;
    public $usuario;
    public $clave;

    public function CrearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO 
        empleados (idTipoEmpleado, idEstado, usuario, clave) 
        VALUES (:idTipoEmpleado, :idEstado, :usuario, :clave)");

        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);

        $consulta->bindValue(':idTipoEmpleado', $this->idTipoEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':idEstado', $this->idEstado, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerTodos()
    {
     
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados");
        $consulta->execute();
        $empleados= array();

        while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){
            $emp= new Empleado();
            $emp->id = $fila['id'];
            $emp->idTipoEmpleado = $fila['idTipoEmpleado'];
            $emp->idEstado = $fila['idEstado'];
            $emp->usuario = $fila['usuario'];
            $emp->clave = $fila['clave'];
            array_push($empleados,$emp);
            var_dump($empleados);
        }
         
        
        return $empleados;
    }

    public static function Obtenerusuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        E.Id as id,
        E.idTipoEmpleado as idTipoEmpleado,
        E.idEstado as idEstado,
        E.usuario as usuario,
        E.clave as clave,
        EE.Estado as estado,
        TE.Tipo as tipoEmpleado
        FROM empleados E
        INNER JOIN estadoempleado EE ON EE.Id = E.idEstado
        INNER JOIN tipoempleado TE ON TE.Id = E.idTipoEmpleado
        WHERE usuario = :usuario");

        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }

    public static function ObtenerEmpleadosPorTipo($idTipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        E.Id as id,
        E.idTipoEmpleado as idTipoEmpleado,
        E.idEstado as idEstado,
        E.usuario as usuario,
        E.clave as clave,
        EE.Estado as estado,
        TE.Tipo as tipoEmpleado
        FROM empleados E
        INNER JOIN estadoempleado EE ON EE.Id = E.idEstado
        INNER JOIN tipoempleado TE ON TE.Id = E.idTipoEmpleado
        WHERE E.idTipoEmpleado = :idTipo");

        $consulta->bindValue(':idTipo', $idTipo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function ObtenerEmpleadoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT 
        E.Id as id,
        E.idTipoEmpleado as idTipoEmpleado,
        E.idEstado as idEstado,
        E.usuario as usuario,
        E.clave as clave,
        EE.Estado as estado,
        TE.Tipo as tipoEmpleado
        FROM empleados E
        INNER JOIN estadoempleado EE ON EE.Id = E.idEstado
        INNER JOIN tipoempleado TE ON TE.Id = E.idTipoEmpleado
        WHERE E.Id = :id");

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }

    public static function ObtenerEmpleadoDisponible($empleados)
    {
        $listaEmpleadoPedidos = array();

        foreach ($empleados as $key => $empleado) 
        {
            $numeroPedidosEmpleado = PedidoProducto::ContarPedidosDeEmpleado($empleado->id);
            $listaEmpleadoPedidos[$empleado->id] = $numeroPedidosEmpleado;
        }

        $minPedidos = min($listaEmpleadoPedidos);
        $idEmpleadoMinPedidos = array_search($minPedidos, $listaEmpleadoPedidos);

        return self::ObtenerEmpleadoPorId($idEmpleadoMinPedidos);
    }


    
    private static function EncontrarEmpleadoEnListaProducto($listaProductoEmpleado, $productoIdBuscado)
    {
        foreach ($listaProductoEmpleado as $keyLista => $keyProducto) 
        {
            if(key($listaProductoEmpleado[$keyLista]) == $productoIdBuscado)
            {
                return $keyProducto[$productoIdBuscado];
            }
        }

        return false;
    }


    
    public static function ObtenerEmpleadosPorProductos($listaProductos)
    {
        $listaProductoEmpleado = array();

        foreach ($listaProductos as $key => $producto) 
        {
            $productoEmpleado = array();
            $idEmpleadoYaAsginado = self::EncontrarEmpleadoEnListaProducto($listaProductoEmpleado, $producto->id);

            if($idEmpleadoYaAsginado == false)
            {
                $empleados = self::ObtenerEmpleadosPorTipo($producto->idTipoEmpleado);
                $empleadoAsignado = self::ObtenerEmpleadoDisponible($empleados);
                $productoEmpleado[$producto->id] = $empleadoAsignado->id;
            }
            else
            {
                $productoEmpleado[$producto->id] = $idEmpleadoYaAsginado;
            }

            array_push($listaProductoEmpleado, $productoEmpleado);
        }

        return $listaProductoEmpleado;
    }


    public static function ToString($empleado)
    {
        return "| Id: " . $empleado->id . " | TipoEmpleado: " . $empleado->tipoEmpleado . " | Estado: " . $empleado->estado . " | usuario: " . $empleado->usuario;
    }

    public static function Validar($usuario)
    {
        $mensajesError = array();

        if(empty(str_replace(' ', '', $usuario)))
        {
            array_push($mensajesError, "usuario invalido.");
        }

        if(Empleado::Obtenerusuario($usuario) != null && Empleado::Obtenerusuario($usuario)->usuario == $usuario)
        {
            array_push($mensajesError, "El usuario". $usuario . " ya existe.");
        }

        return $mensajesError;
    }

   
    public static function borrarusuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }



    //Modificar empleado

    public static function ModificarEmpleado($empleado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET idTipoEmpleado = :idTipoEmpleado, idEstado = :idEstado, usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':id', $empleado->id, PDO::PARAM_INT);
        $consulta->bindValue(':idTipoEmpleado', $empleado->idTipoEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':idEstado', $empleado->idEstado, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $empleado->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $empleado->clave, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function CargarDesdeCSV( $request,  $response, $args){
        $archivo= "./csv/usuarios.csv";

        if(($lector= fopen($archivo, 'r')) !== false){
            $datos= fgetcsv($lector);
            while(($fila=fgetcsv($lector)) !== false){
            
            // Crear un objeto Empleado con los datos de la fila
            $empleado = new Empleado();
            $empleado->id = $datos[0];
            $empleado->idTipoEmpleado = $datos[1];
            $empleado->idEstado = $datos[2];
            $empleado->usuario = $datos[3];
            $empleado->clave = $datos[4];
            $empleado->CrearEmpleado();
        }
        fclose($lector);
    }
    $response->getBody()->write("Se cargo correctamente");
    return $response;
}


function generarArchivoCSV($empleados, $rutaArchivo)
{
    // Abrir el archivo en modo escritura
    $archivo = fopen($rutaArchivo, "wb");

    // Escribir la primera línea con los encabezados de las columnas
    $encabezados = array('ID', 'Nombre', 'Apellido', 'Email');
    fputcsv($archivo, $encabezados);

    // Escribir los datos de los empleados en el archivo
    foreach ($empleados as $empleado) {
        $datosEmpleado = array($empleado->id, $empleado->nombre, $empleado->apellido, $empleado->email);
        fputcsv($archivo, $datosEmpleado);
    }

    // Cerrar el archivo
    fclose($archivo);
}

}