<?php

include_once './models/producto.php';
include_once './DB/AccesoDatos.php';

class AltaProducto
{
    public static function guardarProducto($nombre, $precio, $tipo, $marca, $stock, $imagen)
    {
        $nombreImagen = self::procesarImagen($imagen, $nombre, $tipo);

        if ($nombreImagen) {
            $producto = self::buscarProducto($nombre);
            if($producto == null)
            {
                $producto = new producto($nombre, $precio, $tipo, $marca, $stock, $nombreImagen);
                
                self::guardar($producto);
                return "Producto guardado con exito";
            }
            else
            {
                self::agregarStock($producto, $stock, $precio);
                return "Producto actualizado";
            }
        } else {
            return "La imagen no se pudo guardarS";
        }

    }

    public static function guardar($producto)
    {
        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("INSERT INTO productos 
        (nombre, precio, tipo, marca,  stock, imagen) 
        VALUES (:nombre, :precio, :tipo, :marca, :stock, :imagen)");

        $consulta->bindValue(':nombre', $producto->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $producto->getPrecio(), PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $producto->getTipo(), PDO::PARAM_STR);
        $consulta->bindValue(':marca', $producto->getMarca(), PDO::PARAM_STR);
        $consulta->bindValue(':stock', $producto->getStock(), PDO::PARAM_INT);
        $consulta->bindValue(':imagen', $producto->getImagen(), PDO::PARAM_STR);
        $consulta->execute();

    }

    public static function buscarProducto($nombre)
    {
        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("SELECT * FROM productos WHERE nombre = :nombre");

        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        
        if($resultados)
        {
            foreach($resultados as $resul)
            {
                $producto = new producto(
                    $resul['nombre'],
                    $resul['precio'],
                    $resul['tipo'],
                    $resul['marca'],
                    $resul['stock'],
                    $resul['imagen']
                );

                return $producto;
            }
            
        }

        return null;
    }

    public static function agregarStock($productoViejo, $masStock, $nuevoPrecio)
    {
        $sumaStock = $productoViejo->getStock() + $masStock;

        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("UPDATE productos
        SET precio = :precio, stock = :stock
        WHERE nombre = :nombre");

        $consulta->bindValue(':nombre', $productoViejo->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':precio', $nuevoPrecio, PDO::PARAM_INT);
        $consulta->bindValue(':stock', $sumaStock, PDO::PARAM_INT);
        $consulta->execute();
    }



    private static function procesarImagen($imagen, $nombre, $tipo)
    {
        $uploadDir = __DIR__ . "/ImagenesProductos/2024/";

        // Crear el directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Obtener el nombre y la extensión del archivo subido
        if ($imagen instanceof \Slim\Psr7\UploadedFile) {
            $nombreOriginal = $imagen->getClientFilename();
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

            $nombreImagen = $nombre . "_" . $tipo . "." . "." . $extension;
            $uploadFile = $uploadDir . $nombreImagen;
            $imagen->moveTo($uploadFile);

            return $nombreImagen;
        } else {
            throw new InvalidArgumentException("El archivo de imagen no es válido.");
        }
    }

    static function  existenciaProducto($nombre, $tipo, $marca)
    {
        $producto = self::buscarProducto($nombre);

        if($producto != null)
        {
            if($producto->getTipo() != $tipo)
            {
                return "No existe un producto de ese 'tipo' con el nombre: " . $nombre;
            }
            if($producto->getMarca() != $marca)
            {
                return "No existe un producto de esa 'marca' con el nombre: " . $nombre;
            }
    
            return "El producto con esa marca y tipo existe";
        }else{
            return "El producto con ese nombre no existe";
        }
    }

}




