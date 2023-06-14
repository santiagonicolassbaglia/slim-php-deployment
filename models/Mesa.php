<?php

class Mesa
{
    private $idMesa;
    private $codigo;
    private $estado;
    private $cliente;

    public function __construct($idMesa, $codigo, $estado, $cliente)
    {
        $this->idMesa = $idMesa;
        $this->codigo = $codigo;
        $this->estado = $estado;
        $this->cliente = $cliente;
    }

    public function getIdMesa()
    {
        return $this->idMesa;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function setIdMesa($idMesa)
    {
        $this->idMesa = $idMesa;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function __toString()
    {
        return "Id: $this->idMesa - Codigo: $this->codigo - Estado: $this->estado - Cliente: $this->cliente";
    }

    public function guardarMesa()
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            INSERT INTO mesas (codigo, estado, cliente)
            VALUES (:codigo, :estado, :cliente)
        ");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);

        if ($consulta->execute()) {
            $this->idMesa = $objetoAccesoDato->obtenerUltimoId();
            $retorno = true;
        }

        return $retorno;


    }

    public static function obtenerTodos()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            SELECT idMesa, codigo, estado, cliente
            FROM mesas
        ");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, Mesa::class);
    }

    public static function obtenerUno($idMesa)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            SELECT idMesa, codigo, estado, cliente
            FROM mesas
            WHERE idMesa = :idMesa
        ");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject(Mesa::class);
    }

    public function actualizarMesa()
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            UPDATE mesas
            SET codigo = :codigo,
                estado = :estado,
                cliente = :cliente
            WHERE idMesa = :idMesa
        ");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);

        if ($consulta->execute()) {
            $retorno = true;
        }

        return $retorno;
    }

    public static function borrarMesa($idMesa)
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            DELETE FROM mesas
            WHERE idMesa = :idMesa
        ");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);

        if ($consulta->execute()) {
            $retorno = true;
        }

        return $retorno;
    }


    public static function cambiarEstado($idMesa, $estado)
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            UPDATE mesas
            SET estado = :estado
            WHERE idMesa = :idMesa
        ");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);

        if ($consulta->execute()) {
            $retorno = true;
        }

        return $retorno;
    }

    public static function cambiarCliente($idMesa, $cliente)
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            UPDATE mesas
            SET cliente = :cliente
            WHERE idMesa = :idMesa
        ");
        $consulta->bindValue(':cliente', $cliente, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);

        if ($consulta->execute()) {
            $retorno = true;
        }

        return $retorno;
    }

    public static function cambiarCodigo($idMesa, $codigo)
    {
        $retorno = false;

        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("
            UPDATE mesas
            SET codigo = :codigo
            WHERE idMesa = :idMesa
        ");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);

        if ($consulta->execute()) {
            $retorno = true;
        }

        return $retorno;
    }

    public static function obtenerMesaPorCodigo($codigo)
    {
        $retorno = null;

        $mesas = Mesa::obtenerTodos();

        foreach ($mesas as $mesa) {
            if ($mesa->codigo == $codigo) {
                $retorno = $mesa;
                break;
            }
        }

        return $retorno;
    }

    
} 



?>