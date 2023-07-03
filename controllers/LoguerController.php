<?php
require_once './middlewares/AutentificadorJWT.php';
 
require_once './models/Empleado.php'; 

class LoguerController extends  AutentificadorJWT    
{
    public function GenerarToken($request, $response)
    {
        $datosPost = $request->getParsedBody();
        $datosBD = Empleado::getEmpleadoPorNombre($datosPost["nombre"]);
  
         

        if($datosBD != null && $datosPost["clave"] == $datosBD->clave)
        {
            $datos = array('id'=> $datosBD->id, 'nombre' => $datosBD->nombre, "rol"=> $datosBD->rol);
            $token = AutentificadorJWT::CrearToken($datos);
            $payload = json_encode(array('Se ha logeado como:' => $datosBD->rol, 'Token' => $token));
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