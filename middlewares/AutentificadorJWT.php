<?php

use Firebase\JWT\JWT;
 
class AutentificadorJWT
{
    private static $claveSecreta = 'JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearTokenEmpleado($datos)
    {
        try 
        {
            // Se instancia un objeto de la clase Empleado
            $empleado = new Empleado();
             
            $empleadoExistente = $empleado->ObtenerUsuario($datos['usuario']);
    
            var_dump(   $empleadoExistente , $datos['clave'] , $empleadoExistente->clave, password_verify($datos['clave'], $empleadoExistente->clave) , $datos['clave'] == $empleadoExistente->clave);
          

            if( $datos['clave'] == $empleadoExistente->clave && $datos['usuario'] == $empleadoExistente->usuario)
            {
                $datos['idTipoEmpleado'] = $empleadoExistente->idTipoEmpleado;
    
            // Se llama al método CrearToken de la clase AutentificadorJWT para generar el token JWT con los datos proporcionados y la clave secreta
            return AutentificadorJWT::CrearToken($datos, self::$claveSecreta);
            }
           else {
 
                throw new Exception("Usuario o contraseña inválida");
            }
    
 
        } 
        catch (\Exception $ex) 
        {
            // Si ocurre una excepción durante el proceso, se lanza nuevamente para que sea manejada por el código que llamó a este método
            throw $ex;
        }
    }
 

 
    private static function CrearToken($datos, $clave)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,//cuando se creo el jwt
            'exp' => $ahora + (800000000000000000), // tiempo de vencimiento
            'aud' => self::Aud(),// Audiencia del token (destinatario)
            'data' => $datos,// datos del jwt
            'app' => "JWT"// info de la aplicacion
        );
        return JWT::encode($payload, $clave);
    }


    public static function VerificarToken($token)
    {
        if (empty($token)) 
        {
            throw new Exception("El token está vacío.");
        }
    
        try 
        {
            // Se decodifica el token JWT utilizando la clave secreta y el tipo de encriptación
            $decodificado = JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion);
        } 
        catch (Exception $e) 
        {
            // Si ocurre una excepción durante la decodificación, se lanza nuevamente para que sea manejada por el código que llamó a este método
               ;throw $e;
          
        }
    
        if ($decodificado->aud !== self::Aud()) 
        {
            // Si el campo "aud" del token no coincide con el resultado de la función Aud(), se lanza una excepción indicando que el usuario no está autorizado
            throw new Exception("Usuario no autorizado");
        }
    
        // Se retorna el token decodificado
        return $decodificado;
    }

    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) //Se verifica si el token está vacío y, de ser así, se lanza una excepción indicando que el token está vacío.
        {
            throw new Exception("El token está vacío.");
        }
        
        // Se decodifica el token JWT y se retorna su payload (contenido)
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
{
    // Se decodifica el token JWT y se retorna el campo "data" del payload
    return JWT::decode(
        $token,
        self::$claveSecreta,
        self::$tipoEncriptacion
    )->data;
}
private static function Aud()
{
    $aud = '';

    // Verificar si existe la variable $_SERVER['HTTP_CLIENT_IP'] que contiene la dirección IP del cliente
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
    {
        $aud = $_SERVER['HTTP_CLIENT_IP'];
    } 
    // Verificar si existe la variable $_SERVER['HTTP_X_FORWARDED_FOR'] que contiene la dirección IP real del cliente (en caso de estar detrás de un proxy)
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
    {
        $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } 
    // Si no se cumple ninguna de las condiciones anteriores, utilizar la dirección IP remota del cliente
    else 
    {
        $aud = $_SERVER['REMOTE_ADDR'];
    }

    // Concatenar la variable $_SERVER['HTTP_USER_AGENT'] que contiene la información del agente de usuario (navegador)
    $aud .= @$_SERVER['HTTP_USER_AGENT'];

    // Concatenar el nombre del host del servidor utilizando la función gethostname()
    $aud .= gethostname();

    // Aplicar la función hash sha1() al valor obtenido para generar una cadena de verificación única
    return sha1($aud);
}

public static function GetTokenDelHeader($header)
{
    // Verificar si el encabezado está vacío
    if (empty($header)) 
    {
        throw new Exception("Autorizacion vacía");
    }

    // Dividir el encabezado en dos partes utilizando "Bearer" como separador y tomar la segunda parte (índice 1)
    // Utilizar la función trim() para eliminar espacios en blanco al inicio y final de la cadena resultante
    return trim(explode("Bearer", $header)[1]);
}
}
