<?php
require_once './models/Encuesta.php';
 
class EncuestaController extends Encuesta
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $requiredParams = ['idMesa', 'puntuacionMesa', 'puntuacionRestaurante', 'puntuacionMozo','puntuacionCocinero','comentario'];
    
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
        $mesa = Mesa::GetMesaPorId($parametros["idMesa"]);
        if (  $mesa->estado !='Cerrada') {
            $payload = json_encode(array("error" =>'la mesa deve estar cerrada para hacer la encuesta') );
            $response->getBody()->write($payload);
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    
        $codigoPedido = $parametros["idMesa"];
        $puntuacionMesa = $parametros["puntuacionMesa"];
        $puntuacionRestaurante = $parametros["puntuacionRestaurante"];
        $puntuacionMozo = $parametros["puntuacionMozo"];
        $puntuacionCocinero = $parametros["puntuacionCocinero"];
        $comentario = $parametros["comentario"];

        try
        {
            if(!empty($comentario) && $puntuacionMesa >= 1 && $puntuacionMesa <= 10 && $puntuacionRestaurante >= 1 && $puntuacionRestaurante <= 10 && $puntuacionMozo >= 1 && $puntuacionMozo <= 10 && $puntuacionCocinero >= 1 && $puntuacionCocinero <= 10)
            {
                $encuesta = new Encuesta();
                $encuesta->codigoPedido = $codigoPedido;
                $encuesta->puntuacionMesa = $puntuacionMesa;
                $encuesta->puntuacionRestaurante = $puntuacionRestaurante;
                $encuesta->puntuacionMozo = $puntuacionMozo;
                $encuesta->puntuacionCocinero = $puntuacionCocinero;
                $encuesta->comentario = $comentario;
                $encuesta->altaEncuesta();
                $payload = json_encode(array("Mensaje"=> "Encuesta cargada con exito!"));
            }
            else
            {
                $payload = json_encode(array("Error"=> "Revise los datos! (Puntaciones 1-10)"));
            }
        }
        catch(Throwable $e)
        {
            $payload = json_encode(array("Excepcion"=> $e->getMessage()));
        }
        $response->getBody()->write($payload);
        return $response->withHeader("Content-type", "application/json");
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