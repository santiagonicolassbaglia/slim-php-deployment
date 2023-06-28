<?php
require_once './middlewares/AutentificadorJWT.php';
 
require_once "./models/Usuario.php"; 

class LoginController extends AutentificadorJWT
{
    public function GenerarToken($request, $response)
    {
        $datosPost = $request->getParsedBody();
        $datosBD = Usuario::obtenerUsuario($datosPost["usuario"]);
  
         

        if($datosBD != null && $datosPost["clave"] == $datosBD->clave)
        {
            $datos = array('id'=> $datosBD->id, 'usuario' => $datosBD->usuario, "tipo"=> $datosBD->tipo);
            $token = AutentificadorJWT::CrearTokenEmpleado($datos);
            $payload = json_encode(array('Se ha logeado como:' => $datosBD->tipo, 'Token' => $token));
            $response->getBody()->write($payload);
        }
        else
        {
            $response->getBody()->write(json_encode(array("Error" => "El usuario o la contraseña no coinciden.")));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
 

 
}

?>