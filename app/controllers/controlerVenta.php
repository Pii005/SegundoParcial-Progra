<?php

include_once './models/AltaVentas.php';

class ControlerVenta
{
    public function generarVenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $Archivo = $request->getUploadedFiles();

        if(isset($parametros["email"], $parametros["nombre"], $parametros["tipo"], 
        $parametros["marca"], $parametros["cantidad"]) && isset($Archivo["imagen"]))
        {
            $email = $parametros["email"];
            $nombre = $parametros["nombre"];
            $tipo = $parametros["tipo"];
            $marca = $parametros["marca"];
            $cantidad = $parametros["cantidad"];
            $imagen = $Archivo["imagen"];

            try
            {
                $msg = AltaVentas::generarVenta($email, $nombre, $tipo, $marca, $cantidad, $imagen);

                $payload = json_encode(array("mensaje" => $msg));
            }catch (Exception $e){
                $payload = json_encode(array("Error" => "No se pudo guardar la venta - " . $e->getMessage()));
            }

        }else {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerVentasDelDia($request, $response, $args)
    {
        try
        {
            $msg = AltaVentas::productosVendidos();
            $payload = json_encode(array("mensaje" => $msg));

        }catch (Exception $e){
            $payload = json_encode(array("Error" => "No se pudieron obtener las ventas - " . $e->getMessage()));
        }

        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ventasUser($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if (isset($params["email"])) 
            {
            $email = $params["email"];
            try
            {
                // $msg = Usuario::ObtenerMostrar($email);
                
                $msg = AltaVentas::mostrarVentasUser($email);
                $payload = json_encode(array("mensaje" => $msg));
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo encontrar el usuario"));
            }
            }
        }else 
        {
            $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ventasProducto($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if (isset($params["nombre"])) 
            {
            $nombre = $params["nombre"];
            try
            {
                $msg = AltaVentas::mostrarVentasProducto($nombre);
                $payload = json_encode(array("mensaje" => $msg));
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo encontrar el usuario"));
            }
            }
        }else 
        {
            $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
        }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
    }

    public function ventasEntreValores($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if (isset($params["minimo"]) && isset($params["maximo"])) 
            {
            $minimo = $params["minimo"];
            $maximo = $params["maximo"];

            try
            {
                $msg = AltaVentas::entreValores($minimo, $maximo);
                $payload = json_encode(array("mensaje" => $msg));
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo encontrar el usuario"));
            }
            }
        }else 
        {
            $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
        }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
    }

}