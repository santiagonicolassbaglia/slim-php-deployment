<?php
class Pedido
{
    private $idPedido;
    private $productos;
    private $estado;
    private $tiempoEstimado;
    private $fotoMesa;
    private $idMesa;
    private $idEmpleado;

    public function __construct($idPedido, $productos, $idMesa, $idEmpleado)
    {
        $this->idPedido = $idPedido;
        $this->productos = $productos;
        $this->estado = 'pendiente';
        $this->tiempoEstimado = null;
        $this->fotoMesa = null;
        $this->idMesa = $idMesa;
        $this->idEmpleado = $idEmpleado;
    }

    public function getIdPedido()
    {
        return $this->idPedido;
    }

    public function getProductos()
    {
        return $this->productos;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getTiempoEstimado()
    {
        return $this->tiempoEstimado;
    }

    public function setTiempoEstimado($tiempoEstimado)
    {
        $this->tiempoEstimado = $tiempoEstimado;
    }

    public function getFotoMesa()
    {
        return $this->fotoMesa;
    }

    public function setFotoMesa($fotoMesa)
    {
        $this->fotoMesa = $fotoMesa;
    }

    public function getIdMesa()
    {
        return $this->idMesa;
    }

    public function getIdEmpleado()
    {
        return $this->idEmpleado;
    }

    public function setIdPedido($idPedido)
    {
        $this->idPedido = $idPedido;
    }

    public function setProductos($productos)
    {
        $this->productos = $productos;
    }

    public function setIdMesa($idMesa)
    {
        $this->idMesa = $idMesa;
    }

    public function setIdEmpleado($idEmpleado)
    {
        $this->idEmpleado = $idEmpleado;
    }

    public function __toString()
    {
        return "Id: $this->idPedido - Productos: $this->productos - Estado: $this->estado - Tiempo estimado: $this->tiempoEstimado - Foto de mesa: $this->fotoMesa - Id mesa: $this->idMesa - Id empleado: $this->idEmpleado";
    }

    public function guardarPedido()
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            INSERT INTO pedidos (productos, estado, tiempoEstimado, fotoMesa, idMesa, idEmpleado)
            VALUES (:productos, :estado, :tiempoEstimado, :fotoMesa, :idMesa, :idEmpleado)
        ");
        $consulta->bindValue(':productos', $this->productos, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_STR);
        $consulta->bindValue(':fotoMesa', $this->fotoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);

        if ($consulta->execute()) {
            $this->idPedido = $objetoAccesoDato->obtenerUltimoId();
            $retorno = true;
        }

        return $retorno;

        

    }
}
?>