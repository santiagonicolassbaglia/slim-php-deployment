<?php
require_once './models/Mesa.php';
require_once './models/Pedido.php';

class Encuesta
{
    public $codigoPedido;
    public $puntuacionMesa;
    public $puntuacionRestaurante;
    public $puntuacionMozo;
    public $puntuacionCocinero;
    public $comentario;
    public $fecha;

    public function altaEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigoPedido, puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, comentario, fecha) VALUES (:codigoPedido, :puntuacionMesa, :puntuacionRestaurante, :puntuacionMozo, :puntuacionCocinero, :comentario, :fecha)");
        $fecha = new DateTime();
        $consulta->bindValue(":puntuacionMesa", $this->puntuacionMesa);
        $consulta->bindValue(":fecha", $fecha->format("Y-m-d"));
        $consulta->bindValue(":codigoPedido", $this->codigoPedido);
        $consulta->bindValue(":comentario", $this->comentario);
        $consulta->bindValue(":puntuacionRestaurante", $this->puntuacionRestaurante);
        $consulta->bindValue(":puntuacionMozo", $this->puntuacionMozo);
        $consulta->bindValue(":puntuacionCocinero", $this->puntuacionCocinero);
        
        $consulta->execute();
    }

    public static function getEncuestas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function ObtenerMejoresEncuestas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE puntuacionMesa > 5 AND puntuacionRestaurante > 5 AND puntuacionMozo > 5 AND puntuacionCocinero > 5");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }
}