<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/controlerProductos.php';
require_once './controllers/controlerVenta.php';

require_once './middlewares/middProductos.php';



// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
// tienda/consultar por POST) Nombre, Tipo y Marca. -> si existe o no
$app->group('/tienda', function (RouteCollectorProxy $group) {
  $group->post('/cargar', \ControlerProductos::class . ':cargarProducto');
  $group->post('/consultar',\ControlerProductos::class . ':consultar');
});


// ventas/alta POST -> el email del usuario y el Nombre, Tipo, Marca y Stock venta del producto + imagen
/*con el nombre+tipo+marca+email(solo usuario hasta el @) y fecha de la venta en la carpeta
/ImagenesDeVenta/2024. EN LA BASE DE DATOS SE GUARDA SOLO LA RUTA DE LA IMAGEN. */

$app->group('/ventas', function (RouteCollectorProxy $group) {
  $group->post('/alta', \ControlerVenta::class . ':generarVenta');
  $group->get('/vendidos', \ControlerVenta::class . ':obtenerVentasDelDia');
  $group->get('/porUsuario', \ControlerVenta::class . ':ventasUser');
  $group->get('/porProducto', \ControlerVenta::class . ':ventasProducto');
  $group->get('/entreValores', \ControlerVenta::class . ':ventasEntreValores');

});

// /ventas/consultar - GET
/*
    E- ruta: “/ventas/ingresos” El listado de cantidad de dinero / ganancia por día de una fecha ingresada. Si no se
    ingresa una fecha, se muestran las ganancias totales.
    F- ruta: “/productos/masVendido” Mostrar el producto más vendido.
*/

//ventas/modificar PUT
/*
Debe recibir el número de pedido, el email del usuario, el nombre, tipo, marca y cantidad. Si existe (por numero
de pedido) se modifican el resto de los datos, de lo contrario, informar que no existe ese número de pedido.*/



$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
