<?php

include_once "./models/usuario.php";

class AltaUsuario
{
    public static function buscarEmpleado($mail)
    {
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT mail
            FROM usuarios
            WHERE mail = :Email");
        $consulta->bindValue(':Email', $mail, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if (count($resultados) > 0) {
            return true;
        }  
        
        return false;
    }

    public static function obtenerUsuario($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *
        FROM usuarios WHERE mail = :mail");
        $consulta->bindValue(':mail', $email, PDO::PARAM_STR);
        $consulta->execute();
        
        $user = $consulta->fetch(PDO::FETCH_ASSOC);

        if($user)
        {
            $userNew = new Usuario(
                $user['nombre'],
                $user['clave'],
                $user['mail'],
                $user['foto'],
                $user['perfil']
            );

            $userNew->setId($user['id']);
            $userNew->setAlta($user['alta']);
            $userNew->setBaja($user['baja']);
            return $userNew;
        }
        return null;
    }



    public static function crearUsuario($nombre, $clave, $mail, $perfil, $alta, $foto)
    {
        if(!self::buscarEmpleado($mail))
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre, clave, mail, perfil, alta, foto)
            VALUES (:nombre, :clave, :mail, :perfil, :alta, :foto)");

            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);

            $claveHash = password_hash($clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':clave', $claveHash);

            $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
            $consulta->bindValue(':perfil', $perfil, PDO::PARAM_STR);
            $consulta->bindValue(':alta', $alta->format('Y-m-d'), PDO::PARAM_STR);


            $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
            $consulta->execute();
            // echo "creado con exito";
            return "Usuario creado";
        }
        else
        {
            return "El usuario ya existe";
        }
        //return $objAccesoDatos->obtenerUltimoId();
    }

    private static function procesarImagen($imagen, $email, $puesto)
    {
        $uploadDir = __DIR__ . "/ImagenesUsuarios/2024/";

        // Crear el directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Obtener el nombre y la extensión del archivo subido
        if ($imagen instanceof \Slim\Psr7\UploadedFile) {
            $nombreOriginal = $imagen->getClientFilename();
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

            $nombreImagen = $email . "_" . $puesto . "." . $extension;
            $uploadFile = $uploadDir . $nombreImagen;
            $imagen->moveTo($uploadFile);

            return $nombreImagen;
        } else {
            throw new InvalidArgumentException("El archivo de imagen no es válido.");
        }
    }

    static function creacionUser($nombre, $clave, $mail, $perfil, $foto)
    {
        $rutaImagen = self::procesarImagen($foto, $mail, $perfil);
        $alta = new DateTime();

        return self::crearUsuario($nombre, $clave, $mail, $perfil, $alta, $rutaImagen);
    }

}