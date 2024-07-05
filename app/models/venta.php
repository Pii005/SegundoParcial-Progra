<?php

class Venta
{
    private $_id;
    private $_email;
    private $_nombre;
    private $_tipo;
    private $_marca;
    private $_stock;
    private $_imagen;
    private $_fechaVenta;
    private $_numeroPedido;
    private $total;
    private $_borrado; // Nuevo atributo

    public function __construct($email, $nombre, $tipo, $marca, $stock, $total, $imagen, $numeroPedido)
    {
        $this->_email = $email;
        $this->_nombre = $nombre;
        $this->_tipo = $tipo;
        $this->_marca = $marca;
        $this->_stock = $stock;
        $this->total = $total;
        $this->_imagen = $imagen;
        $this->_fechaVenta = new DateTime();
        $this->_numeroPedido = $numeroPedido;
        $this->_borrado = false; // Establecer el valor predeterminado a false
    }

    // Getters
    public function getId() {
        return $this->_id;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getNombre() {
        return $this->_nombre;
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
    public function getTottal() {
        return $this->total;
    }

    public function getImagen() {
        return $this->_imagen;
    }

    public function getFecha() {
        return $this->_fechaVenta;
    }

    public function getNumeroPedido() {
        return $this->_numeroPedido;
    }

    public function getBorrado() {
        return $this->_borrado;
    }

    // Setters
    public function setEmail($email) {
        $this->_email = $email;
    }

    public function setId($fecha) {
        $this->_id = $fecha;
    }
    public function setNombre($nombre) {
        $this->_nombre = $nombre;
    }

    public function setTipo($tipo) {
        $this->_tipo = $tipo;
    }

    public function setMarca($marca) {
        $this->_marca = $marca;
    }

    public function setStock($stock) {
        $this->_stock = $stock;
    }

    public function setImagen($imagen) {
        $this->_imagen = $imagen;
    }

    public function setFecha($fecha) {
        $this->_fechaVenta = $fecha;
    }

    public function setNumeroPedido($numeroPedido) {
        $this->_numeroPedido = $numeroPedido;
    }

    public function cambiarBorrado()
    {
        $this->_borrado = true;
    }


    // Convert to array
    public function toArray() {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'nombre' => $this->getNombre(),
            'tipo' => $this->getTipo(),
            'marca' => $this->getMarca(),
            'stock' => $this->getStock(),
            'total' => $this->getTottal(),
            'imagen' => $this->getImagen(),
            'fecha' => $this->getFecha(),
            'numeroPedido' => $this->getNumeroPedido(),
            
        ];
    }

    private static function generarNumeroDePedido() {
        $fecha = date("YmdHis");
        $microsegundos = microtime(true);
        $microsegundos = explode('.', $microsegundos);
        $milisegundos = str_pad($microsegundos[1], 3, '0', STR_PAD_RIGHT);
        return $fecha . $milisegundos;
    }

    public function mostrar()
    {
        return "ID: " . $this->getId() . "<br>" .
        "Email: " . $this->getEmail() . "<br>" .
        "Nombre: " . $this->getNombre() . "<br>" .
        "Tipo: " . $this->getTipo() . "<br>" .
        "Marca: " . $this->getMarca() . "<br>" .
        "Stock: " . $this->getStock() . "<br>" .
        "Imagen: " . $this->getImagen() . "<br>" .
        "Fecha: " . $this->getFecha() . "<br>" .
        "Número de Pedido: " . $this->getNumeroPedido() . "<br>";
    }

}

