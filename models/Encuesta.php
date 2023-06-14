<?php

class Encuesta
{
    private $idEncuesta;
    private $puntuacionMesa;
    private $puntuacionRestaurante;
    private $puntuacionMozo;
    private $puntuacionCocinero;
    private $comentario;
    private $idPedido;

    public function __construct($idEncuesta, $puntuacionMesa, $puntuacionRestaurante, $puntuacionMozo, $puntuacionCocinero, $comentario, $idPedido)
    {
        $this->idEncuesta = $idEncuesta;
        $this->puntuacionMesa = $puntuacionMesa;
        $this->puntuacionRestaurante = $puntuacionRestaurante;
        $this->puntuacionMozo = $puntuacionMozo;
        $this->puntuacionCocinero = $puntuacionCocinero;
        $this->comentario = $comentario;
        $this->idPedido = $idPedido;
    }

    public function getIdEncuesta()
    {
        return $this->idEncuesta;
    }

    public function getPuntuacionMesa()
    {
        return $this->puntuacionMesa;
    }

    public function getPuntuacionRestaurante()
    {
        return $this->puntuacionRestaurante;
    }

    public function getPuntuacionMozo()
    {
        return $this->puntuacionMozo;
    }

    public function getPuntuacionCocinero()
    {
        return $this->puntuacionCocinero;
    }

    public function getComentario()
    {
        return $this->comentario;
    }

    public function getIdPedido()
    {
        return $this->idPedido;
    }

    public function setIdEncuesta($idEncuesta)
    {
        $this->idEncuesta = $idEncuesta;
    }

    public function setPuntuacionMesa($puntuacionMesa)
    {
        $this->puntuacionMesa = $puntuacionMesa;
    }

    public function setPuntuacionRestaurante($puntuacionRestaurante)
    {
        $this->puntuacionRestaurante = $puntuacionRestaurante;
    }

    public function setPuntuacionMozo($puntuacionMozo)
    {
        $this->puntuacionMozo = $puntuacionMozo;
    }

    public function setPuntuacionCocinero($puntuacionCocinero)
    {
        $this->puntuacionCocinero = $puntuacionCocinero;
    }

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }

    public function setIdPedido($idPedido)
    {
        $this->idPedido = $idPedido;
    }


    public function guardarEncuesta()
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            INSERT INTO encuestas (puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, comentario, idPedido)
            VALUES (:puntuacionMesa, :puntuacionRestaurante, :puntuacionMozo, :puntuacionCocinero, :comentario, :idPedido)
        ");

        $consulta->bindValue(':puntuacionMesa', $this->puntuacionMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionRestaurante', $this->puntuacionRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionMozo', $this->puntuacionMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionCocinero', $this->puntuacionCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);

        if ($consulta->execute()) {
            $retorno = true;
        }

        return $retorno;
    }

    public static function obtenerEncuestas()
    {
        $encuestas = array();

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            SELECT idEncuesta, puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, comentario, idPedido
            FROM encuestas
        ");

        $consulta->execute();

        while ($encuesta = $consulta->fetchObject('Encuesta')) {
            $encuestas[] = $encuesta;
        }

        return $encuestas;
    }

 
    public static function obtenerEncuestaPorId($idEncuesta)
    {
        $encuesta = null;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            SELECT idEncuesta, puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, comentario, idPedido
            FROM encuestas
            WHERE idEncuesta = :idEncuesta
        ");

        $consulta->bindValue(':idEncuesta', $idEncuesta, PDO::PARAM_INT);
        $consulta->execute();

        if ($consulta->rowCount() == 1) {
            $encuesta = $consulta->fetchObject('Encuesta');
        }

        return $encuesta;
    }

    public static function obtenerEncuestaPorIdPedido($idPedido)
    {
        $encuesta = null;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            SELECT idEncuesta, puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, comentario, idPedido
            FROM encuestas
            WHERE idPedido = :idPedido
        ");

        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        if ($consulta->rowCount() == 1) {
            $encuesta = $consulta->fetchObject('Encuesta');
        }

        return $encuesta;
    }


    public static function obtenerEncuestasPorIdPedido($idPedido)
    {
        $encuestas = array();

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            SELECT idEncuesta, puntuacionMesa, puntuacionRestaurante, puntuacionMozo, puntuacionCocinero, comentario, idPedido
            FROM encuestas
            WHERE idPedido = :idPedido
        ");

        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        while ($encuesta = $consulta->fetchObject('Encuesta')) {
            $encuestas[] = $encuesta;
        }

        return $encuestas;



}
}

?>