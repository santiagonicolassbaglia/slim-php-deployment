<?php

class Pedido
{
    public $id;
    public $idEmpleado;
    public $idProducto;
    public $cantidadProductos;
    public $idMesa;
    public $estado;
    public $codigoPedido;
    public $fotoMesa;
    public $tiempoPreparacion;
    public $horaCreacion;
    public $horaFinalizacion;

    public function AltaPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idEmpleado, idProducto, cantidadProductos, idMesa, estado, codigoPedido, fotoMesa, tiempoPreparacion, horaCreacion) VALUES (:idEmpleado, :idProducto, :cantidadProductos, :idMesa, :estado, :codigoPedido, :fotoMesa, :tiempoPreparacion, :horaCreacion)");
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidadProductos', $this->cantidadProductos, PDO::PARAM_INT);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado);
        $consulta->bindValue(':fotoMesa', $this->fotoMesa);
        $consulta->bindValue(':codigoPedido', $this->codigoPedido);
        $consulta->bindValue(':tiempoPreparacion', $this->tiempoPreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':horaCreacion', date_format($this->horaCreacion, 'H:i:sa'));
        $consulta->execute();
    }

    public static function GetPedidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function GetPedidoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchObject("Pedido");
    }

    public static function GetPedidosSegunSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id, pedidos.estado, pedidos.codigoPedido, productos.descripcion, pedidos.tiempoPreparacion FROM pedidos INNER JOIN productos ON pedidos.idProducto = productos.id WHERE sector = :sector AND estado = 'Pendiente'");
        $consulta->bindValue(':sector', $sector);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public static function GetPedidosPreparados($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id, pedidos.estado, pedidos.codigoPedido, productos.descripcion, pedidos.tiempoPreparacion FROM pedidos INNER JOIN productos ON pedidos.idProducto = productos.id WHERE sector = :sector AND estado = 'En preparacion'");
        $consulta->bindValue(':sector', $sector);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public static function ModificarPedido($idEmpleado, $idPedido, $estado, $tiempoPreparacion)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, idEmpleado = :idEmpleado, tiempoPreparacion = :tiempoPreparacion WHERE id = :id");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':tiempoPreparacion', $tiempoPreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function ModificarEstadoPedido($idPedido, $estado, $horaFinalizacion)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, horaFinalizacion = :horaFinalizacion WHERE id = :id");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':horaFinalizacion', date_format($horaFinalizacion, 'H:i:sa'));
        $consulta->execute();
    }

    public static function GetDemora($idMesa, $codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempoPreparacion, estado, descripcion FROM pedidos INNER JOIN productos ON pedidos.idProducto = productos.id WHERE idMesa = :idMesa AND codigoPedido = :codigoPedido");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public static function GetPedidosListos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado = :estado");
        $consulta->bindValue(':estado', "Listo para servir!");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function TraerPrecios($codigoPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT productos.precio, pedidos.cantidadProductos FROM productos INNER JOIN pedidos ON productos.id = pedidos.idProducto WHERE pedidos.codigoPedido = :codigoPedido");
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll();
    }

    public static function MesaMasUsada()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT idMesa, COUNT(idMesa) AS `cantidad` FROM pedidos GROUP BY idMesa ORDER BY `cantidad` DESC LIMIT 1");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public static function GetPedidosPendientes($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.*
        FROM pedidos
        join productos on productos.id = pedidos.idProducto
        WHERE pedidos.estado = :estado and productos.sector = :sector");
        $consulta->bindValue(':estado', "Pendiente");
        $consulta->bindValue(':sector', $sector);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
 }


 public static function GetPedidoscodigoMesa($idMesa,$codigoPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * 
        FROM pedidos 
        WHERE codigoPedido = :codigoPedido and idMesa =:idMesa" );
        $consulta->bindValue(':codigoPedido', $codigoPedido);
        $consulta->bindValue(':idMesa', $idMesa);
        $consulta->execute();
        return $consulta->fetchObject('Pedido');
    }
     


    public static function GetPedidosPreparacion($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.* 
        FROM pedidos
        join productos on productos.id = pedidos.idProducto
        WHERE pedidos.estado = :estado and productos.sector = :sector");
        $consulta->bindValue(':estado', "En preparacion");
        $consulta->bindValue(':sector', $sector);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
   }



   public static function ListoParaServir($idPedido)
   {
       $objAccesoDato = AccesoDatos::obtenerInstancia();
       $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
       SET estado = :estado, horaFinalizacion = :horaFinalizacion 
       WHERE id = :id");
       $consulta->bindValue(':estado', 'Listo para servir', PDO::PARAM_STR);
       $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
       $consulta->bindValue(':horaFinalizacion', date('H:i:sa'));
       $consulta->execute();
   }




   public static function ServirPedido($idPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
        SET estado = :estado 
        WHERE id = :id");
        $consulta->bindValue(':estado', 'Entregado', PDO::PARAM_STR);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function VerificarPedidosRetrasados($tiempoEstipulado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'En preparacion' AND ADDTIME(horaCreacion, SEC_TO_TIME(horaFinalizacion - horaCreacion )) < :tiempoPreparacion");
        $consulta->bindValue(':tiempoEstipulado', $tiempoEstipulado);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }



    public static function CuentaMesa($idMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT productos.precio, pedidos.cantidadProductos
         FROM productos 
         INNER JOIN pedidos ON productos.id = pedidos.idProducto 
         WHERE pedidos.idMesa = :idMesa");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_STR);
        $consulta->execute();
        $venta = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $totalPagar=0;
        
        foreach ($venta as $element){
            $totalPagar+= intval($element['cantidadProductos'])*intval($element['precio']);
        }
        return $totalPagar;
   }
}

?>