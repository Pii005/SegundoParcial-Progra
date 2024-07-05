<?php

include_once './models/AltaUsuario.php';
include_once './JWT/creacionjwt.php';

class ControlerUsuario
{
    public function ingresarUsuario($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $Archivo = $request->getUploadedFiles();


        if(isset($parametros['nombre']) && isset($parametros["clave"]) 
        && isset($parametros["mail"]) && isset($parametros["perfil"]) 
        && isset($Archivo["foto"]))
        {
        $nombre = $parametros['nombre'];
        $perfil = $parametros["perfil"];
        $mail = $parametros["mail"];
        $clave = $parametros["clave"];
        $foto = $Archivo["foto"];
        
        try
        {
            
            $msg = AltaUsuario::creacionUser($nombre, $clave, $mail, $perfil, $foto);
            $payload = json_encode(array("mensaje" => $msg));
        }
        catch(Exception $e)
        {
            $payload = json_encode(array("Error" => "No se pudo crear el usuario"));
        }
        }
        else 
        {
            $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function loginUser($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['email']))
        {
        $email = $parametros['email'];
        
        $user = AltaUsuario::obtenerUsuario($email);

        if($user != null)
        {
            $datos = array('mail' => $email, 'perfil' => $user->getPerfil());
            $token = AutentificadorJWT::CrearToken($datos);
    
            $payload = json_encode(array('jwt' => $token));
        }
        else{
            $payload = json_encode(array('Error' => "Usuario no encontrado"));

        }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}