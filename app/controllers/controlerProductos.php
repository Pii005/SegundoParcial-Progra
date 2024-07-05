<?php

include_once './models/AltaProducto.php';

class ControlerProductos
{
    public function cargarProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $Archivo = $request->getUploadedFiles();


        if (isset($parametros["nombre"], $parametros["precio"], $parametros["tipo"], 
        $parametros["marca"], $parametros["stock"]) && isset($Archivo["imagen"])) {
            $nombre = $parametros["nombre"];
            $precio = $parametros["precio"];
            $tipo = $parametros["tipo"];
            $marca = $parametros["marca"];
            $stock = $parametros["stock"];
            $imagen = $Archivo["imagen"];
            
            try
            {
                $msg = AltaProducto::guardarProducto($nombre, $precio, $tipo, $marca, $stock, $imagen);
                $payload = json_encode(array("mensaje" => $msg));

            }catch (Exception $e){
                $payload = json_encode(array("Error" => "No se pudo guardar el producto - " . $e->getMessage()));
            }
        }else {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function consultar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $Archivo = $request->getUploadedFiles();


        if (isset($parametros["nombre"],$parametros["tipo"],$parametros["marca"])) {
            $nombre = $parametros["nombre"];
            $tipo = $parametros["tipo"];
            $marca = $parametros["marca"];
            
            try
            {
                $msg = AltaProducto::existenciaProducto($nombre, $tipo, $marca);
                $payload = json_encode(array("mensaje" => $msg));

            }catch (Exception $e){
                $payload = json_encode(array("Error" => "No se pudo encontrar el producto - " . $e->getMessage()));
            }
        }else {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    
}


