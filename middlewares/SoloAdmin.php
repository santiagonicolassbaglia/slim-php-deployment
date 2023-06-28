<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class SoloAdmin
{
    public function __invoke(Request $request,RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

        try
        {
            $data = AutentificadorJWT::ObtenerData($token);
            if($data->tipo == "admin")
            {
                $response= $handler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(array('Error' => "Accion reservada solamente para los administradores.")));
            }
        }
        catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>