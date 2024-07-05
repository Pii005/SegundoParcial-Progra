<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './JWT/creacionjwt.php';
require_once './Enumerados/tiposEmpleados.php';


class VerificadorPuestos
{
    public function verificarAdmis(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

        // $tokenDescode = '{'.$tokenDescode.'}';

        $data = json_decode(json_encode($tokenDescode), true);
        $datosUser = $data['data'];

        if($datosUser['perfil'] == "admin")
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No tiene permiso para esta operacion'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verificarMozo(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

        // $tokenDescode = '{'.$tokenDescode.'}';

        $data = json_decode(json_encode($tokenDescode), true);
        $datosUser = $data['data'];

        if($datosUser['perfil'] == "empleado" || $datosUser['perfil'] == "admin"
        || $datosUser['perfil'] == "socio")
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No tiene permiso para esta operacion'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    
    public function verificarSocios(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

        // $tokenDescode = '{'.$tokenDescode.'}';

        $data = json_decode(json_encode($tokenDescode), true);
        $datosUser = $data['data'];

        if($datosUser['pefil'] == "socio" || $datosUser['pefil'] =="admin")
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No tiene permiso para esta operacion'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


}


