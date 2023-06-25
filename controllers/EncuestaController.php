<?php
require_once './models/Encuesta.php';
 
class EncuestaController extends Encuesta
{
    public function CargarUno($request, $response, $args)
{
    $parametros = $request->getParsedBody();

    $requiredParams = ['idMesa', 'idPedido', 'puntuacionMesa', 'puntuacionRestaurante','puntuacionMozo','puntuacionCocinero','comentarios'];

    $missingParams = [];
    foreach ($requiredParams as $param) {
        if (!isset($parametros[$param])) {
            $missingParams[] = $param;
        }
    }

    if (!empty($missingParams)) {
        $payload = json_encode(array("error" => "Falta el campo: " . implode(', ', $missingParams)));
        $response->getBody()->write($payload);
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }

    $idMesa = $parametros['idMesa'];
    $idPedido = $parametros['idPedido'];
    $puntuacionMesa = $parametros['puntuacionMesa'];
    $puntuacionRestaurante = $parametros['puntuacionRestaurante'];
    $puntuacionMozo = $parametros['puntuacionMozo'];
    $puntuacionCocinero = $parametros['puntuacionCocinero'];
    $comentarios = $parametros['comentarios'];


    $encuesta = new Encuesta();
    $encuesta->idMesa = $idMesa;
    $encuesta->idPedido = $idPedido;
    $encuesta->puntuacionMesa = $puntuacionMesa;
    $encuesta->puntuacionRestaurante = $puntuacionRestaurante;
    $encuesta->puntuacionMozo = $puntuacionMozo;
    $encuesta->puntuacionCocinero = $puntuacionCocinero;
    $encuesta->comentarios = $comentarios;

    $nuevoId = $encuesta->CrearEncuesta();

    $payload = json_encode(array("mensaje" => "La encuesta ah sido creado", "id" => $nuevoId));

    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json');
}

    public function MejoresEncuestas($request, $response, $args)
    {
        try 
        {
            $encuestas = Encuesta::ObtenerMejoresEncuestas();

            if(count($encuestas) < 1)
            {
                throw new Exception("No se encontraron buenas encuestas.");
            }

            $payload = json_encode($encuestas);
        } 
        catch (Exception $ex) 
        {
            $mensaje = $ex->getMessage(); 
            $payload = json_encode(array('Error' => $mensaje));
        }
        finally
        {
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}