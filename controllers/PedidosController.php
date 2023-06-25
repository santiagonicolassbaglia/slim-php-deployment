<?php
require_once './models/Pedido.php';
require_once './models/PedidoProducto.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
 
require_once './interfaces/IApiUsable.php';

class PedidosController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
{
    $parametros = $request->getParsedBody();

    $idMesa = $parametros['idMesa'];
    $idCliente = $parametros['idCliente'];
    $idsProductos = $parametros['idsProductos'];
    $arrayIdsProductos = explode(",", $idsProductos);
    $foto = $_FILES['foto'];

    $payload = null;

    // Validar y procesar la foto
    if ($foto['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = $foto['name'];
        $archivoTemporal = $foto['tmp_name'];
        $directorioDestino = "./Fotos/" . $nombreArchivo;

        // Mover el archivo al directorio destino
        if (move_uploaded_file($archivoTemporal, $directorioDestino)) {
           
            $payload = json_encode(array("mensaje" => "El pedido se ha creado exitosamente."));
        } else {
            // Ocurrió un error al guardar la foto
            $payload = json_encode(array("error" => "Ocurrió un error al guardar la foto."));
        }
    } else {
        // Ocurrió un error en la subida de la foto
        $payload = json_encode(array("error" => "Ocurrió un error al subir la foto."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
}

    public function Cobrar($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $idPedido = $parametros['idPedido'];
      $payload = null;

      try 
        {
          $pedido = Pedido::ObtenerPorId($idPedido);

          if($pedido == null)
          {
            throw new Exception("No existe el pedido buscado.");
          }

          $mesa = Mesa::ObtenerPorId($pedido->idMesa);
          $mesa->estadoId = 3;
          Mesa::PutMesa($mesa);

          $payload = json_encode(array('Respuesta' => "Se cobro con exito."));
        } 
        catch (Exception $ex) 
        {
            $mensaje = $ex->getMessage();

            if($ex->getCode() == 800)
            {
                $mensaje = json_decode($ex->getMessage());
            }

            $payload = json_encode(array('Error' => $mensaje));
        }
        finally
        {
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

    }

    public function CerrarPedidoMesa($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $idPedido = $parametros['idPedido'];
      $payload = null;

      try 
        {
          $pedido = Pedido::ObtenerPorId($idPedido);
          $pedido->fechaFin = date('Y-m-d H:i:s');
          Pedido::ModificarPedido($pedido);

          if($pedido == null)
          {
            throw new Exception("No existe el pedido buscado.");
          }

          $mesa = Mesa::ObtenerPorId($pedido->idMesa);
          $mesa->estadoId = 4;
          Mesa::PutMesa($mesa);

          $payload = json_encode(array('Respuesta' => "Se cerro con exito la mesa."));
        } 
        catch (Exception $ex) 
        {
            $mensaje = $ex->getMessage();

            if($ex->getCode() == 800)
            {
                $mensaje = json_decode($ex->getMessage());
            }

            $payload = json_encode(array('Error' => $mensaje));
        }
        finally
        {
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

    }

    public function TraerUno($request, $response, $args)
    {
        $codigoPedido = $args['codigo'];
        $lista = Pedido::ObtenerPorCodigo($codigoPedido);
        $lista->listaProductosPedidos = PedidoProducto::ObtenerPorCodigoPedido($codigoPedido);

        $payload = json_encode(array("pedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::ObtenerTodos();

        if(count($lista) > 0)
        {
          foreach ($lista as $key => $pedido) 
          {
            $pedido->listaProductosPedidos = PedidoProducto::ObtenerPorCodigoPedido($pedido->codigo);
          }
        }

        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');

      }
          
     
    
          public function BorrarUno($request, $response, $args)
        {
            $parametros = $request->getParsedBody();
            $id = $parametros['id'];
            $payload = null;

            try 
            {
                $pedido = Pedido::ObtenerPorId($id);

                if($pedido == null)
                {
                    throw new Exception("No existe el pedido buscado.");
                }

                Pedido::BorrarPedido($id);

                $payload = json_encode(array('Respuesta' => "Se borro con exito."));
            } 
            catch (Exception $ex) 
            {
                $mensaje = $ex->getMessage();

                if($ex->getCode() == 800)
                {
                    $mensaje = json_decode($ex->getMessage());
                }

                $payload = json_encode(array('Error' => $mensaje));
            }
            finally
            {
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
             


        }
    
        public function ModificarUno($request, $response, $args)
        {

            $parametros = $request->getParsedBody();
            $id = $parametros['id'];
            $estadoId = $parametros['estadoId'];
            $codigo = $parametros['codigo'];
            $idMesa = $parametros['idMesa'];
            $idCliente = $parametros['idCliente'];
            $fechaInicio = $parametros['fechaInicio'];
            $fechaFin = $parametros['fechaFin'];
            $foto = $_FILES['foto'];
            $payload = null;

            try 
            {
                $pedido = Pedido::ObtenerPorId($id);

                if($pedido == null)
                {
                    throw new Exception("No existe el pedido buscado.");
                }

                $pedido->estadoId = $estadoId;
                $pedido->codigo = $codigo;
                $pedido->idMesa = $idMesa;
                $pedido->idCliente = $idCliente;
                $pedido->fechaInicio = $fechaInicio;
                $pedido->fechaFin = $fechaFin;

                Pedido::ModificarPedido($pedido);

                $payload = json_encode(array('Respuesta' => "Se modifico con exito."));
            } 
            catch (Exception $ex) 
            {
                $mensaje = $ex->getMessage();

                if($ex->getCode() == 800)
                {
                    $mensaje = json_decode($ex->getMessage());
                }

                $payload = json_encode(array('Error' => $mensaje));
            }
            finally
            {
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

             
        }
}
