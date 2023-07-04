<?php
require_once "./models/Mesa.php";
require_once "./models/Pedido.php";

class MesasController extends Mesa
{
    public static $estados = array("con cliente esperando pedido", "con cliente comiendo", "con cliente pagando", "cerrada");
    public function CargarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];
$numeroMesa = $parametros['numeroMesa'];

        if(in_array($estado, $this::$estados))
        {
            $producto = new Mesa();
            $producto->estado = $estado;
$producto->numeroMesa = $numeroMesa;
            $producto->AltaMesa();
            $payload = json_encode(array("Mensaje" => "Mesa creado con exito"));
        }
        else
        {
            $payload = json_encode(array("Mensaje" => "Estado de mesa no valido. (con cliente esperando pedido / con cliente comiendo / con cliente pagando / cerrada)"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function MostrarMesas($request, $response, $args)
    {
        $lista = Mesa::GetMesas();
        $payload = json_encode(array("Mesas" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CambiarEstadoMesa($request, $response, $args)
    {
        //$parametros = $request->getParsedBody();
        $mesa = Mesa::GetMesaPorId($args['idMesa']);
        $listaPedidos = Pedido::GetPedidos();

 
        foreach ($listaPedidos as $pedido)
        {
            if($pedido->idMesa == $mesa->id && $pedido->estado == "Entregado")
            { var_dump("entro");
                Mesa::CambiarEstado($mesa->id, "con cliente comiendo");
                $response->getBody()->write("Se ha modificado el estado de la mesa con exito!\n");
                break;
            }
        }
        return $response->withHeader('Content-Type', 'application/json');
  }

  
    public function AbrirMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $mesa = Mesa::GetMesaPorId($parametros['idMesa']);
        Mesa::CambiarEstado($mesa->id, "con cliente esperando pedido");
        $response->getBody()->write("Mesa abierta con exito!");
        return $response->withHeader('Content-Type', 'application/json');
    }



    public function cobrar($request, $response, $args)
    {
        //$parametros = $request->getParsedBody();
        $mesa = Mesa::GetMesaPorId($args['idMesa']);
        $totalPagar = Pedido::CuentaMesa($args['idMesa']);
        var_dump($totalPagar);
            if($mesa->estado == "con cliente comiendo")
            {
                Mesa::CambiarEstado($mesa->id, "con cliente pagando");
                $totalPagar = Pedido::CuentaMesa($args['idMesa']);
                $response->getBody()->write("Se ha modificado el estado de la mesa con exito! total a pagar: ' $totalPagar'\n");
                
            }else{
                $response->getBody()->write(" no Se ha modificado el estado de la mesa\n");
            }
        
        return $response->withHeader('Content-Type', 'application/json');
        }


    public function CerrarMesa($request, $response, $args)
    {
        //$parametros = $request->getParsedBody();
        $mesa = Mesa::GetMesaPorId($args['idMesa']);
        
            if($mesa->estado == "con cliente pagando")
            {
                Mesa::CambiarEstado($mesa->id, "Cerrada");
                $response->getBody()->write("Se ha modificado el estado de la mesa con exito!\n");
                
            }else{
                $response->getBody()->write(" no Se ha modificado el estado de la mesa\n");
            }
        
        return $response->withHeader('Content-Type', 'application/json');
  }


  public function masUsada($request, $response, $args)
    {
        $mesa = Mesa::GetMesasMasUsada();
        $payload = json_encode( $mesa);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
  }
}

?>