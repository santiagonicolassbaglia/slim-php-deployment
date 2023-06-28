<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ConToken
{
    public function __invoke(Request $request,RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine(("Authorization")); //
        if(!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
        }
        else
        {
            $token = "";
        }
        $response = new Response(); //
        try
        {
            json_encode(array("Token" => AutentificadorJWT::ValidarToken($token)));
            $response = $handler->handle($request); 
        }
        catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>