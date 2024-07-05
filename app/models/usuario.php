<?php

class Usuario
{
    private $id;
    private $nombre;
    private $clave;
    private $mail;
    private $perfil;
    private $alta;
    private $baja;
    private $foto;

    public function __construct($nombre, $clave, $mail, $foto, $perfil)
    {
        $this->nombre = $nombre;
        $this->clave = $clave;
        $this->mail = $mail;
        $this->alta = new DateTime(); 
        $this->perfil = $perfil;
        $this->foto = $foto;
    }

    public function Mostrar()
    {
        $data = array(
            "ID" => $this->id,
            "nombre" => $this->nombre,
            "Ingreso" => $this->alta,
            "perfil" => $this->perfil
        );
        
        if($this-> baja != "0000-00-00")
        {
            $data["Baja"] =  $this->baja;
        }
        return $data;
    }
    public function getFotol()
    {
        return $this->foto;
    }
    
    public function getPerfil()
    {
        return $this->perfil;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getclave()
    {
        return $this->clave;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRegistro()
    {
        return $this->alta;
    }

    public function setId($a)
    {
        $this->id = $a;
    }

    public function setAlta($a)
    {
        $this->alta = $a;
    }
    public function setbaja($a)
    {
        $this->baja = $a;
    }
}
