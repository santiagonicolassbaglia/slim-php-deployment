<?php



    class Empleado
{
    private $idEmpleado;
    private $nombre;
    private $sector;
    private $rol;

    public function __construct($idEmpleado, $nombre, $sector, $rol)
    {
        $this->idEmpleado = $idEmpleado;
        $this->nombre = $nombre;
        $this->sector = $sector;
        $this->rol = $rol;
    }

    public function getIdEmpleado()
    {
        return $this->idEmpleado;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getSector()
    {
        return $this->sector;
    }

    public function getRol()
    {
        return $this->rol;
    }


    public function crearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Empleados (idEmpleado,nombre, sector, rol ) VALUES (:idEmpleado,:nombre, :sector, :rol)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado',$this->idEmpleado, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM Empleados');
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Empleado::class);
    }

    public static function obtenerPorId($idEmpleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM Empleados WHERE idEmpleado = :idEmpleado');
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject(Empleado::class);
    }

    public function actualizar()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE Empleados SET nombre = :nombre, sector = :sector, rol = :rol WHERE idEmpleado = :idEmpleado');
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function eliminar($idEmpleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('DELETE FROM Empleados WHERE idEmpleado = :idEmpleado');
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerPorSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM Empleados WHERE sector = :sector');
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Empleado::class);
    }

    public static function obtenerPorRol($rol)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM Empleados WHERE rol = :rol');
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Empleado::class);
    }

    public static function obtenerPorSectorYRol($sector, $rol)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM Empleados WHERE sector = :sector AND rol = :rol');
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Empleado::class);
    }

    public static function obtenerPorSectorYRolDisponible($sector, $rol)
    {
        $empleados = Empleado::obtenerPorSectorYRol($sector, $rol);
        $empleadosDisponibles = array();

        foreach ($empleados as $empleado) {
            if ($empleado->estaDisponible()) {
                $empleadosDisponibles[] = $empleado;
            }
        }

        return $empleadosDisponibles;
    }

    public function estaDisponible()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM Pedidos WHERE idEmpleado = :idEmpleado AND estado = "en preparacion"');
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount() === 0;
    }

    public static function obtenerPorIdPedido($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            'SELECT e.* FROM Empleados e
            INNER JOIN Pedidos p ON p.idEmpleado = e.idEmpleado
            WHERE p.idPedido = :idPedido'
        );
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject(Empleado::class);
    }

    
}

?>