<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './middlewares/AutentificadorJWT.php';


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
            if($data->rol == "Mozo")
            {
                $response= $handler->handle($request);
            } 

            if ($data->rol == "Socio")
            {
                $response= $handler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(array('Error' => "Esta accion no le pertenece.")));
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