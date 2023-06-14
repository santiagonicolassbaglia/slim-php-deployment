<?php

class Cliente
{
    private $id;
    private $nombre;
    private $email;
    private $pedidos;

    public function __construct($id, $nombre, $email)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->pedidos = [];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPedidos()
    {
        return $this->pedidos;
    }

    public function agregarPedido(Pedido $pedido)
    {
        $this->pedidos[] = $pedido;
    }






    public function crearCliente()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Clientes (id,nombre, email  ) VALUES (:id,:nombre, :email )");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
         $consulta->bindValue(':id',$this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, Cliente, clave FROM Clientes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cliente');
    }

    public static function obtenerCliente($Cliente)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre FROM Clientes WHERE Cliente = :Cliente");
        $consulta->bindValue(':Cliente', $Cliente, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Cliente');
    }

    public static function modificarCliente($id, $Cliente, $clave)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE Clientes SET Cliente = :Cliente, clave = :clave WHERE id = :id");
        $consulta->bindValue(':Cliente', $Cliente, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    
    public static function borrarCliente($Cliente)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE Clientes SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $Cliente, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }









    // public function guardarEnBaseDeDatos()
    // {
        
    //     // $connection = ... // Conexión a la base de datos
    //     // $query = "INSERT INTO clientes (id, nombre, email) VALUES (:id, :nombre, :email)";
    //     // $statement = $connection->prepare($query);
    //     // $statement->bindParam(":id", $this->id);
    //     // $statement->bindParam(":nombre", $this->nombre);
    //     // $statement->bindParam(":email", $this->email);
    //     // $statement->execute();
    // }

    // public function validarDatos()
    // {
       
    // }
} 



?>