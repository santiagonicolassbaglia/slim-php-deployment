<?php
 
 use Firebase\JWT\JWT;

 use Psr\Http\Message\ServerRequestInterface as Request;
 use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
 use Slim\Psr7\Response;
 use Slim\Routing\RouteContext;
class Validaciones
  { 
    public function ValidarJWT( $request,  $handler) 
    {

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
      var_dump($header);
        $response = new Response();

        try
        { 
          
              AutentificadorJWT::VerificarToken($token);
                $response= $handler->handle($request);
         
         }
         catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function ValidarSocio( $request,  $handler) 
    {

        $header = $request->getHeaderLine(("Authorization"));
        //$token = trim(explode("Bearer", $header)[1]);
        sscanf($header, 'Bearer %s', $token);
        $response = new Response();
 
        try
        {
          
          if($token != 'null')
          { 
             $data = AutentificadorJWT::ObtenerData($token); 

          
            if($data->tipo == "Socio")
            {
                $response= $handler->handle($request);
            }
            else
            {       
                $response->getBody()->write(json_encode(array('Error' => "Accion reservada solamente para los Socios.")));
            }
        }else
        {
            $response->getBody()->write(json_encode(array('Error' => "Token invalido.")));
        }
    }
         catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarVendedor( $request,  $handler) 
    {

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

        try
        {
          
          if($token != 'null')
          { 
             $data = AutentificadorJWT::ObtenerData($token); 

          
            if($data->tipo == "Mozos")
            {
                $response= $handler->handle($request);
            }
            else
            {       
                $response->getBody()->write(json_encode(array('Error' => "Accion reservada solamente para los Mozos.")));
            }
        }else
        {
            $response->getBody()->write(json_encode(array('Error' => "Token invalido.")));
        }
    }
         catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
  




   public function TablaBorrados($request, $handler)
   {
 

    $routeContext = RouteContext::fromRequest($request);
    $route = $routeContext->getRoute();

   
    if (!empty($route)) {
       
        $routeArgs = $route->getArguments();

     
       
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (id_usuario, id_arma,accion, fecha_accion)  VALUES (:id_usuario, :id_arma, :accion, :fecha_accion )"); 
 
        $consulta->bindValue(':id_usuario',  $routeArgs['idUsuario'], PDO::PARAM_STR);
        $consulta->bindValue(':id_arma', $routeArgs['idArma'], PDO::PARAM_INT);
        $consulta->bindValue(':accion', 'borrar');
        $consulta->bindValue(':fecha_accion', date("Y-m-d"));
    
    
        $consulta->execute();
    }
     $response = $handler->handle($request);


    return $response;
   } 

}
?>





