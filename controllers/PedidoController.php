<?php
require_once "./models/Pedido.php";
require_once "./models/Producto.php";
require_once "./models/Mesa.php";
require_once "./middlewares/AutentificadorJWT.php";

class PedidoController extends Pedido
{
    public function CargarPedido($request, $response, $args)
    {$data = $request->getAttribute('jwt');
        $parametros = $request->getParsedBody();
        $idProducto = Producto::GetProductoPorId($parametros['idProducto']);
        $cantidadProductos = $parametros['cantidadProductos'];
        $idMesa = Mesa::GetMesaPorId($parametros['idMesa']);
     $codigoPedido = substr(bin2hex(random_bytes(3)), 0, 5);
var_dump($data);

        if($idProducto != null && $idMesa != null)
        {
            $pedido = new Pedido();
            $pedido->idEmpleado = $data->id;
            $pedido->idProducto = $parametros['idProducto'];
            $pedido->cantidadProductos = $cantidadProductos;
            $pedido->idMesa = $parametros['idMesa'];
            $pedido->estado = "Pendiente";
            $pedido->codigoPedido = $codigoPedido;
            $pedido->tiempoPreparacion = 0;
       
            $pedido->horaCreacion = new DateTime(date("h:i:sa"));
            
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['fotoMesa'])) {
                $foto = $uploadedFiles['fotoMesa'];
                if ($foto->getError() === UPLOAD_ERR_OK) {
                    // Obtener información del archivo
                    $nombreArchivo = $foto->getClientFilename();
                    $nuevaUbicacion = './Imagenes/' . $nombreArchivo;
                    $foto->moveTo($nuevaUbicacion);
                } else {
                    // Manejar el error de carga del archivo
                    $error = $foto->getError();
                    $payload = json_encode(array("error" => "Error al cargar el archivo: " . $error));
                    $response->getBody()->write($payload);
                    return $response
                        ->withStatus(400)
                        ->withHeader('Content-Type', 'application/json');
                }
            } else {
                // Manejar el caso en el que no se haya proporcionado el campo "foto"
                $payload = json_encode(array("error" => "Falta el campo: foto"));
                $response->getBody()->write($payload);
                return $response
                    ->withStatus(400)
                    ->withHeader('Content-Type', 'application/json');
            }
            $pedido->fotoMesa = $nuevaUbicacion;
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
        $sector =$data->rol;
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
        $sector =$data->rol;
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
        
        $dataJwt = $request->getAttribute('jwt');
        $parametros = $request->getParsedBody();

        $idPedido = $parametros['idPedido'];
        $tiempoPreparacion = $parametros['tiempoPreparacion'];
        $pedido= Pedido::GetPedidoPorId($idPedido);
        var_dump($pedido->estado == "Pendiente" 
        && Producto::GetProductoPorId($pedido->idProducto)->sector == $dataJwt->rol);
        var_dump($pedido);
        var_dump( Producto::GetProductoPorId($pedido->idProducto));
       
       
        if($pedido->estado == "Pendiente" 
        
        && Producto::GetProductoPorId($pedido->idProducto)->sector == $dataJwt->rol)
        {
            Pedido::ModificarPedido($dataJwt->id, $idPedido, "En preparacion", $tiempoPreparacion);
            $response->getBody()->write("Pedido modificado con exito!");
        }
        else
        {
            $response->getBody()->write("No se ha modificado el pedido.");
        }
        return $response->withHeader('Content-Type', 'application/json');
 }
    public function MostrarPedidosPendientes($request, $response, $args)
    {
        $dataJwt = $request->getAttribute('jwt');
        $lista = Pedido::GetPedidosPendientes($dataJwt->rol);
        $payload = json_encode(array("Pedidos" => $lista));
        $response->getBody()->write($payload);
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




    public function mostrarMesacodigo($request, $response, $args)
    {
        $pedido = Pedido::GetPedidoscodigoMesa($args['idMesa'],$args['codigoMesa']);
        $payload = json_encode($pedido);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }




    public function MostrarPedidosPreparacion($request, $response, $args)
    {
        $dataJwt = $request->getAttribute('jwt');
        $lista = Pedido::GetPedidosPreparacion($dataJwt->rol);
        $payload = json_encode(array("Pedidos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    public function paraServir($request, $response, $args)
    {
        
        $dataJwt = $request->getAttribute('jwt');
        $parametros = $request->getParsedBody();

        $idPedido = $parametros['idPedido'];
        $pedido= Pedido::GetPedidoPorId($idPedido);
      var_dump($pedido);
      var_dump($dataJwt);
      if($pedido->estado == "En preparacion" && ($dataJwt->rol =='Socio' || $pedido->idEmpleado == $dataJwt->id))
        {
            //Pedido::ModificarPedido($dataJwt->id, $idPedido, "En preparacion", $tiempoPreparacion);
            Pedido::ListoParaServir($parametros['idPedido']);
            $response->getBody()->write("Pedido modificado con exito!");
        }
        else
        {
            $response->getBody()->write("No se ha modificado el pedido.");
        }
        return $response->withHeader('Content-Type', 'application/json');
    }



    public function MostrarPedidosParaServir($request, $response, $args)
    {
        $lista = Pedido::GetPedidosListos();
        $payload = json_encode(array("Pedidos" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    public function servir($request, $response, $args)
    {
        
        $dataJwt = $request->getAttribute('jwt');
        $parametros = $request->getParsedBody();

        $idPedido = $parametros['idPedido'];
        $pedido= Pedido::GetPedidoPorId($idPedido);
      var_dump($pedido);
        var_dump($dataJwt);
        if($pedido->estado == "Listo para servir!" && ($dataJwt->rol =='Socio' || $dataJwt->rol =='Mozo'))
        {
            //Pedido::ModificarPedido($dataJwt->id, $idPedido, "En preparacion", $tiempoPreparacion);
            Pedido::ServirPedido($idPedido);
            $response->getBody()->write("Pedido modificado con exito!");
        }
        else
        {
            $response->getBody()->write("No se ha modificado el pedido.");
        }
        return $response->withHeader('Content-Type', 'application/json');
    }





}

?>