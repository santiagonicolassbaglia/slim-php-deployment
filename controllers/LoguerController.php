<?php
require_once './middlewares/AutentificadorJWT.php';
 
require_once './models/Empleado.php'; 

class LoginController extends  AutentificadorJWT    
{
    public function GenerarToken($request, $response)
    {
        $datosPost = $request->getParsedBody();
        $datosBD = Empleado::GetEmpleadoPorNombre($datosPost["nombre"]);
        $clave = $datosPost["clave"];

        if($datosBD != null && md5($clave) == $datosBD->clave)
        {
            
                $datos = array('id'=> $datosBD->id, 'nombre' => $datosBD->nombre, "rol"=> $datosBD->rol);
                $token = AutentificadorJWT::CrearTokenEmpleado($datos);
                $payload = json_encode(array('Se ha logeado como:'=> $datosBD->rol, 'Token' => $token));
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