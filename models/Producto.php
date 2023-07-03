<?php

class Producto
{
    public $id;
    public $descripcion;
    public $precio;
    public $sector;

    public function AltaProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, precio, sector) VALUES (:descripcion, :precio, :sector)");
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function GetProductos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function GetProductoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject("Producto");
    }

    public static function GuardarCSV($path)
    {
        $array = Archivos::ImportarCSV($path);
        for($i = 0; $i < sizeof($array); $i++)
        {
            $datos = explode(",", $array[$i]); 
            $producto = new Producto();
            $producto->id = $datos[0];
            $producto->descripcion = $datos[1];
            $producto->precio = $datos[2];
            $producto->sector = $datos[3];
            $producto->AltaProducto();
        }
    }

}

?>