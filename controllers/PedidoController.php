<?php
require_once "./models/Pedido.php";
require_once "./models/Producto.php";
require_once "./models/Mesa.php";
require_once "./middlewares/AutentificadorJWT.php";

class PedidoController extends Pedido
{
    public function CargarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idProducto = Producto::GetProductoPorId($parametros['idProducto']);
        $cantidadProductos = $parametros['cantidadProductos'];
        $idMesa = Mesa::GetMesaPorId($parametros['idMesa']);
        $codigoPedido = $parametros['codigoPedido'];

        if($idProducto != null && $idMesa != null)
        {
            $pedido = new Pedido();
            $pedido->idEmpleado = 0;
            $pedido->idProducto = $parametros['idProducto'];
            $pedido->cantidadProductos = $cantidadProductos;
            $pedido->idMesa = $parametros['idMesa'];
            $pedido->estado = "Pendiente";
            $pedido->codigoPedido = $codigoPedido;
            $pedido->tiempoPreparacion = 0;
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $pedido->horaCreacion = new DateTime(date("h:i:sa"));
            if(file_exists($_FILES["fotoMesa"]["tmp_name"]))
            {
                $pedido->fotoMesa = $this->MoverFoto($pedido->codigoPedido);
            }
            $pedido->AltaPedido();
            $payload = json_encode(array("Mensaje" => "Pedido creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "El producto o la mesa no existen!"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    
    public function MostrarPedidos($request, $response, $args)
    {
        $lista = Pedido::GetPedidos();
        $payload = json_encode(array("Pedidos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function MostrarPedidosEmpleado($request, $response, $args)
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        $sector = Pedido::ValidarPedido($data->rol);
        $lista = Pedido::GetPedidosSegunSector($sector);
        if(count($lista) > 0)
        {
            $payload = json_encode(array("Pedidos" => $lista));
            $response->getBody()->write($payload);
        }
        else
        {
            $response->getBody()->write(array("Aviso" => "No hay pedidos disponibles para su sector."));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function MostrarPedidosPreparados($request, $response, $args)
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        $sector = Pedido::ValidarPedido($data->rol);
        $lista = Pedido::GetPedidosPreparados($sector);
        try
        {
            if(count($lista) > 0)
            {
                $payload = json_encode(array("Pedidos" => $lista));
            }
            else
            {
                $payload = json_encode(array("Error" => "No hay pedidos disponibles para su sector."));
            }
        }
        catch(Exception $ex)
        {
            
        }
        finally
        {
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function PrepararPedido($request, $response, $args)
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];
        $tiempoPreparacion = $parametros['tiempoPreparacion'];
        
        if((Pedido::GetPedidoPorId($idPedido))->estado == "Pendiente" 
        && Producto::GetProductoPorId((Pedido::GetPedidoPorId($idPedido))->idProducto)->sector == Pedido::ValidarPedido($data->rol))
        {
            Pedido::ModificarPedido($data->id, $idPedido, "En preparacion", $tiempoPreparacion);
            $response->getBody()->write("Pedido modificado con exito!");
        }
        else
        {
            $response->getBody()->write("No se ha modificado el pedido.");
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    private function MoverFoto($codigo)
    {
        $fotosMesa = ".".DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR;
        if(!file_exists($fotosMesa))
        {
            mkdir($fotosMesa, 0777, true);
        }
        $nombreFoto = $fotosMesa."fotoMesa-".$codigo.".jpg";
        if(!file_exists($nombreFoto))
        {
            rename($_FILES["fotoMesa"]["tmp_name"], $nombreFoto);
        }
        return $nombreFoto;
    }
    public function ConsultarDemoraPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idMesa = $parametros["idMesa"];
        $codigoPedido = $parametros["codigoPedido"];

        if(!empty($idMesa) && !empty($codigoPedido))
        {
            $lista = Pedido::GetDemora($idMesa, $codigoPedido);
            if(count($lista) > 0)
            {
                $payload = json_encode(array("Pedidos" => $lista));
                $response->getBody()->write($payload);
            }
            else
            {
                $response->getBody()->write("No se han encontrado pedidos con ese numero de Mesa y Codigo.");
            }
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CambiarEstadoListo($request, $response, $args)
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];
        $pedido = Pedido::GetPedidoPorId($idPedido);
        if($pedido->estado == "En preparacion" && (Pedido::GetPedidoPorId($idPedido))->idEmpleado == $data->id)
        {
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $horaFinalizacion = new DateTime(date("h:i:sa"));
            Pedido::ModificarEstadoPedido($idPedido, "Listo para servir!", $horaFinalizacion);
            $response->getBody()->write("Estado de pedido modificado con exito!");
        }
        else
        {
            $response->getBody()->write("No se ha modificado el estado del pedido (El estado actual debe ser: En preparacion, y debe corresponder el empleado).");
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarPedidosListos($request, $response, $args)
    {
        $lista = Pedido::GetPedidosListos();
        if(count($lista) > 0)
        {
            $payload = json_encode(array("Pedidos" => $lista));
            $response->getBody()->write($payload);
        }
        else
        {
            $response->getBody()->write("Por el momento no hay pedidos listos.");
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarMesaPopular($request, $response, $args)
    {
        $mesa = Pedido::MesaMasUsada();
        $payload = json_encode($mesa);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


}

?>