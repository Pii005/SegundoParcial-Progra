<?php

include_once './models/AltaProducto.php';
include_once './models/venta.php';


class AltaVentas
{
    static function generarVenta($email, $nombre, $tipo, $marca, $cantidad, $imagen)
    {
        //Prodcuto exista
        $producto = AltaProducto::buscarProducto($nombre);
        if($producto != null)
        {
            //tenga stock disponible
            if(self::revisarStock($producto, $cantidad))
            {
                //procesar Imgane
                $rutaImagen = self::procesarImagen($imagen, $nombre, $tipo, $marca, $email);
                //generar ID
                $idVenta = self::crearID();
                //calcular total
                $total = self::calcularPrecio($producto, $cantidad);
                //guardar venta
                $venta = new Venta($email, $nombre, $tipo,$marca, $cantidad, $total, $rutaImagen, $idVenta);
                self::guardarVenta($venta);
                return "Venta guardada con exito";
            }else {
                return "No hay stock suficiente";
            }

        }else{
            return "El producto no existe";
        }

    }

    public static function crearID()
    {
        $longitud = 5;
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $codigo;
    }

    static function revisarStock($producto, $cantidad)
    {
        if($producto->getStock() >= $cantidad)
        {
            return true;
        }
        return false;
    }

    private static function procesarImagen($imagen, $nombre, $tipo, $marca, $email)
    {
        $uploadDir = __DIR__ . "/ImagenesDeVenta/2024/";

        // Crear el directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Obtener el nombre y la extensión del archivo subido
        if ($imagen instanceof \Slim\Psr7\UploadedFile) {
            $nombreOriginal = $imagen->getClientFilename();
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

            $mailDesarmado = self::desarmarEmail($email);

            $nombreImagen = $nombre . "_" . $tipo . "_" . $marca . "_" . $mailDesarmado. "." . "." . $extension;
            $uploadFile = $uploadDir . $nombreImagen;
            $imagen->moveTo($uploadFile);

            return $nombreImagen;
        } else {
            throw new InvalidArgumentException("El archivo de imagen no es válido.");
        }
    }

    public static function desarmarEmail($mail)
    { 
        $pos = strpos($mail, '@');    
        
        if ($pos !== false) {
            $palabra = substr($mail, 0, $pos);
            return $palabra;
        } else {
            return $mail; 
        }
    }

    static function guardarVenta($venta)
    {
        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("INSERT INTO ventas 
        (email, nombreProducto, tipo, marca, cantidad, total, imagen, fechaVenta, numeroPedido, borrado) 
        VALUES (:email, :nombreProducto, :tipo, :marca, :cantidad, :total, :imagen, :fechaVenta, :numeroPedido, :borrado)");

        $consulta->bindValue(':email', $venta->getEmail(), PDO::PARAM_STR);
        $consulta->bindValue(':nombreProducto', $venta->getNombre(), PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $venta->getTipo(), PDO::PARAM_STR);
        $consulta->bindValue(':marca', $venta->getMarca(), PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $venta->getStock(), PDO::PARAM_INT);
        $consulta->bindValue(':total', $venta->getTottal(), PDO::PARAM_INT);
        $consulta->bindValue(':imagen', $venta->getImagen(), PDO::PARAM_STR);
        $consulta->bindValue(':fechaVenta', $venta->getFecha()->format('Y-m-d'), PDO::PARAM_STR);
        $consulta->bindValue(':numeroPedido', $venta->getNumeroPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':borrado', $venta->getBorrado(), PDO::PARAM_STR);

        $consulta->execute();
    }

    static function calcularPrecio($producto, $cantidad)
    {
        return $producto->getPrecio() * $cantidad;
    }


    public static function ventasDelDia($dia)
    {
        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("SELECT * FROM ventas WHERE fechaVenta = :fechaVenta");

        $consulta->bindValue(':fechaVenta', $dia->format('Y-m-d '), PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if($resultados)
        {
            $ventasDeldia = [];
            foreach($resultados as $venta)
            {
                $newventa = new Venta(
                    $venta['email'],
                    $venta['nombreProducto'],
                    $venta['tipo'],
                    $venta['marca'],
                    $venta['cantidad'],
                    $venta['total'],
                    $venta['imagen'],
                    $venta['numeroPedido']
                );
                $newventa->setId($venta['id']);
                $newventa->setFecha($venta['fechaVenta']);
                $ventasDeldia[] = $newventa;

            }
            return $ventasDeldia;
        }
        return null;
    }

    public static function obtenerDiaAnterior()
    {
        $hoy = new DateTime();
        $hoy->modify('-1 day');
        return $hoy;
    }

    public static function productosVendidos()
    {
        $hoy = new DateTime();
        $ventasHoy = self::ventasDelDia($hoy);

        if ($ventasHoy !== null) {
            return self::formatVentas($ventasHoy);
        }

        $ayer = self::obtenerDiaAnterior();
        $ventasAyer = self::ventasDelDia($ayer);

        if ($ventasAyer !== null) {
            return self::formatVentas($ventasAyer);
        }

        return "No hay ventas disponibles ni de hoy ni ayer";
    }

    private static function formatVentas($ventas)
    {
        $ventasFormateadas = [];
        foreach ($ventas as $v) {
            $ventasFormateadas[] = $v->toArray();
        }
        return $ventasFormateadas;
    }

    public static function ventasPorUser($email)
    {
        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("SELECT * FROM ventas WHERE email = :email");

        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if ($resultados) {
            $ventasDeldia = [];
            foreach ($resultados as $venta) {
                $newVenta = new Venta(
                    $venta['email'],
                    $venta['nombreProducto'],
                    $venta['tipo'],
                    $venta['marca'],
                    $venta['cantidad'],
                    $venta['total'],
                    $venta['imagen'],
                    $venta['numeroPedido']
                );
                $newVenta->setId($venta['id']);
                $newVenta->setFecha($venta['fechaVenta']); 
                $ventasDeldia[] = $newVenta;
            }
            return $ventasDeldia;
        }
        return null;
    }

    public static function mostrarVentasUser($email)
    {
        $ventas = self::ventasPorUser($email);
        return self::formatVentas($ventas);
    }

    public static function ventasPorProducto($nombre)
    {
        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("SELECT * FROM ventas WHERE nombreProducto = :nombreProducto");

        $consulta->bindValue(':nombreProducto', $nombre, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if ($resultados) {
            $ventasDeldia = [];
            foreach ($resultados as $venta) {
                $newVenta = new Venta(
                    $venta['email'],
                    $venta['nombreProducto'],
                    $venta['tipo'],
                    $venta['marca'],
                    $venta['cantidad'],
                    $venta['total'],
                    $venta['imagen'],
                    $venta['numeroPedido']
                );
                $newVenta->setId($venta['id']);
                $newVenta->setFecha($venta['fechaVenta']); 
                $ventasDeldia[] = $newVenta;
            }
            return $ventasDeldia;
        }
        return null;
    }

    public static function mostrarVentasProducto($nombre)
    {
        $ventas = self::ventasPorUser($nombre);
        if($ventas != null)
        {
            return self::formatVentas($ventas);
        }
        return "No hay ventas con ese producto";
    }

    public static function ventasTodas()
    {
        $acceso = AccesoDatos::obtenerInstancia();
        $consulta = $acceso->prepararConsulta("SELECT * FROM ventas ");

        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if ($resultados) {
            $ventasDeldia = [];
            foreach ($resultados as $venta) {
                $newVenta = new Venta(
                    $venta['email'],
                    $venta['nombreProducto'],
                    $venta['tipo'],
                    $venta['marca'],
                    $venta['cantidad'],
                    $venta['total'],
                    $venta['imagen'],
                    $venta['numeroPedido']
                );
                $newVenta->setId($venta['id']);
                $newVenta->setFecha($venta['fechaVenta']); 
                $ventasDeldia[] = $newVenta;
            }
            return $ventasDeldia;
        }
        return null;
    }

    static function entreValores($valorMinimo, $valorMaximo)
    {
        $ventas = self::ventasTodas();

        if($ventas)
        {
            $ventasEntreValores = [];
            foreach($ventas as $v)
            {
                if($v->getTottal() > $valorMinimo && $v->getTottal() < $valorMaximo)
                {
                    $ventasEntreValores[] = $v->toArray();
                }
            }
            return $ventasEntreValores;
        }
        return "No hay ventas guardadas";
    }

}
