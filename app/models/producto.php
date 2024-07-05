<?php


class producto
{

    private $_id;
    private $_nombre;
    private $_precio;
    private $_tipo;
    private $_marca;
    private $_stock;
    private $_imagen;

    public function __construct( $nombre, $precio, $tipo, $marca, $stock, $imagen) {
        $this->_nombre = $nombre;
        $this->_precio = $precio;
        $this->_tipo = $tipo;
        $this->_marca = $marca;
        $this->_stock = $stock;
        $this->_imagen = $imagen;
    }

    public static function verificaciones($tipo) { //MIddleware
        if ($tipo != "Smartphone" && $tipo != "Tablet”") {
            echo "El tipo esta incorrecto";
            return false;
        }
        
        return true;
    }

    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getNombre() {
        return $this->_nombre;
    }

    public function getPrecio() {
        return $this->_precio;
    }

    public function getTipo() {
        return $this->_tipo;
    }

    public function getMarca() {
        return $this->_marca;
    }

    public function getStock() {
        return $this->_stock;
    }

    public function setStock($stock) {
        $this->_stock = $stock;
    }

    public function toArray() {
        return [
            'id' => $this->_id,
            'nombre' => $this->_nombre,
            'precio' => $this->_precio,
            'tipo' => $this->_tipo,
            'marca' => $this->_marca,
            'stock' => $this->_stock
        ];
    }

    public function getImagen()
    {
        return $this->_imagen;
    }

}

