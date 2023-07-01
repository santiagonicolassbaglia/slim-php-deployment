<?php

class Factura
{
    public $codigoPedido;
    public $montoTotal;
    public $fecha;
    public $pagada;

    public function altaFactura()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO facturas (codigoPedido, montoTotal, fecha, pagada) VALUES (:codigoPedido, :montoTotal, :fecha, :pagada)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':montoTotal', $this->montoTotal, PDO::PARAM_INT);
        $fecha = new DateTime();
        $consulta->bindValue(':fecha', $fecha->format("Y-m-d"));
        $consulta->bindValue(':pagada', $this->pagada, PDO::PARAM_BOOL);
        $consulta->execute();
    }

    public static function getFacturas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM facturas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Factura');
    }
}
?>
