<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once './models/producto.php';

class MiddProductos
{
    function __invoke(Request $request, RequestHandler $handler) : Response
    {
        $parametros = $request->getParsedBody();

        $response = new Response();
        if(isset($parametros["tipo"]))
        {
            if ($parametros["tipo"] != "Smartphone" && $parametros["tipo"] != "Tablet”") {
                $payload = json_encode(array("Error" => "El tipo esta incorrecto"));
            }
            else{
                return $handler->handle($request);
            }
        }else {
            $payload = json_encode(array("Error" => "parametros no validos"));
        } 
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}