<?php

use GuzzleHttp\Psr7\Response;

require_once './middlewares/AutentificadorJWT.php';
class Logger
{

    
    private static $idSocio = 1;
    private static $idCervecero = 2;
    private static $idCocinero = 3;
    private static $idMozo = 4;
    private static $idBartender = 5;
    public static function LogOperacion($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }
    public static function VerificarCredenciales($request, $handler)
    {
        $response = new Response();
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        $errores = "";

        
        try 
        {
            $headerAuth = $request->getHeaderLine('Authorization');
          //  $token = AutentificadorJWT::GetTokenDelHeader($headerAuth);
    //var_dump($headerAuth); 
    
    $token = substr($headerAuth, 7);
   
    var_dump($token);

            $usuarioEsValido = Logger::VerificarAccesoEndpoint($method, $path, $headerAuth);

            if($usuarioEsValido == true)
            {
                return $handler->handle($request);
            }

            $errores .= "Usuario no autorizado";
        } 
        catch (Exception $ex) 
        {
            $errores .= $ex->getMessage();
        }
        
      $payload = json_encode(array("error" => $errores));
if ($payload === false) {
    $error = json_last_error_msg();
    echo "Error al codificar JSON: " . $error;
} else {
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
}
    }

    private static function VerificarAccesoEndpoint($method, $path, $token)
    {
        switch($method)
        {
            case 'GET':
                return Logger::VerficarGets($path, $token);
            case 'POST':
                return Logger::VerifcarPost($path, $token);
        }
    }  
    private static function VerficarGets($path, $token)
{
    switch($path)
    {
        case '/empleados':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
        case '/productos':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
        case '/mesas':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
        case '/mesas/disponibles':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
        case '/pedidos':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio));
        case '/pedidos/csv/descarga':
            return Logger::UsuarioAutorizado($token);
        case '/pedidos-productos/estado/empleado':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idCervecero, self::$idCocinero, self::$idBartender));
        case '/pedidos-productos/estado':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
        case '/encuesta':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio));
        case '/mesas/estadistica/usadas':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio));
        default:
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
    }
}


private static function VerifcarPost($path, $token)
{
    switch($path)
    {
        case '/empleados':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio));
        case '/productos':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
        case '/mesas':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio));
        case '/mesas/estado':
            return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
            case '/pedidos':
                return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
            case '/pedidos/cobrar':
                return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
            case '/pedidos/estado/cerrar':
                return Logger::UsuarioAutorizado($token, array(self::$idSocio));
            case '/cliente':
                return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
            case '/pedidos-productos/estado/empleado':
                return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idCervecero, self::$idCocinero, self::$idBartender));
            default:
                return Logger::UsuarioAutorizado($token, array(self::$idSocio, self::$idMozo));
        }
    }
    private static function UsuarioAutorizado($token, $arrayIds = null)
    {
        $esValido = true; // Variable para almacenar el resultado de la autorización, inicializada en true por defecto
        $decodificado = AutentificadorJWT::VerificarToken($token); // Decodifica el token y obtiene los datos del usuario
        $id = $decodificado->data->idTipoEmpleado; // Obtiene el ID del tipo de empleado del token decodificado
    
        if ($arrayIds != null && count($arrayIds) > 0) {
            // Verifica si se proporcionó un array de IDs y si contiene elementos
            $esValido = in_array($id, $arrayIds); // Verifica si el ID del tipo de empleado está presente en el array de IDs permitidos
        }
    
        return $esValido; // Devuelve true si el usuario está autorizado, de lo contrario, devuelve false
    }
    

}