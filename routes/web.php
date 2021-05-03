<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->post('/user/token3', 'MagentoController@token3');
$router->post('/use/token3', 'OrdenController@token3');
$router->post('/inicioDeSesion', 'InicioSesionController@enviarInicioSesion');


$router->post('/enviarGravity', 'GravityFormsController@enviarGravityForm');
$router->post('/enviarGravityType1', 'GravityFormsController@enviarGravityForm2');

$router->get('/obtenerMaps', 'MapsController@cosultaMaps');
$router->get('/obtenerTaller2', 'TallerController@cosultaTaller');
$router->get('/obtenerTaller', 'TallerController2@cosultaTaller');

$router->get('/obtenerSlider', 'SliderController@cosultaSlider2');
//OK
$router->get('/producto/all', 'ProductoController@producto');

$router->get('/user/categoria', 'MagentoController@categoria');

$router->get('/productoPorCategoria/all', 'ProductoPorCategoriaController@productoPorCategoria');

$router->get('/compras/orden', 'OrdenController@orden');

//Wordpress
$router->get('/productoAll', ['uses' => 'ProductoWooController@consultaProductos']);
$router->get('/prodAtributos', ['uses' => 'ProductoWooController@consultaProdAtributos']);
$router->get('/categoriaAll', ['uses' => 'CategoriaWooController@consultaCategoria']);

$router->get('/categoriaAll2', ['uses' => 'CategoriaWooController@consultaCategoria2']);
$router->get('/obtenerCategoriaxId', ['uses' => 'CategoriaWooController@obtenerCategoriaxId']);



$router->get('/categoriaProducto', ['uses' => 'CategoriaWooController@categoriaxProducto']);
$router->get('/productDestacado', ['uses' => 'ProductoWooDestacados@consultaProdDestacado']);


$router->get('/magento', ['uses' => 'ServicioImagenesController@llamarVistaMagento']);
$router->get('/administrador', ['uses' => 'ServicioImagenesController@llamarVistaWordpress']);
$router->get('/ckeditor', ['uses' => 'ServicioImagenesController@llamarVistaCkeditor']);
$router->get('/marca', ['uses' => 'ServicioImagenesController@llamarVistaMarca']);



$router->post('/metodoWordpress', ['uses' => 'ServicioImagenesController@implementarFormularioWordpress']);
$router->post('/metodoMagento', ['uses' => 'ServicioImagenesControllerMa@implementarFormularioMagento']);
$router->post('/metodoGarantia', ['uses' => 'ServicioImagenesControllerGa@implementarFormularioGarantia']);
$router->post('/metodoGarantiaEditar', ['uses' => 'ServicioImagenesControllerGa@implementarModalEditar']);


//$router->post('/conexionB', ['uses' => 'ServicioImagenesController@conexionBase']);
$router->post('/delete', ['uses' => 'ConsultasController@deleteGarantia']);
$router->post('/insertarMarca', ['uses' => 'ConsultasController@insertarMarca']);
$router->post('/actualizarMarca', ['uses' => 'ConsultasController@actualizarMarca']);
$router->post('/eliminarMarca', ['uses' => 'ConsultasController@eliminarMarca']);

$router->get('/comboWordPress', ['uses' => 'ConsultasController@cosultaComboWordPress']);
$router->get('/comboMagento', ['uses' => 'ConsultasController@cosultaMagento']);
$router->get('/llamarGarantias', ['uses' => 'ConsultasController@cosultaGarantia']);
$router->get('/llamarMarca', ['uses' => 'ConsultasController@cosultaMarca']);

$router->get('/rutaWordpress', ['uses' => 'ConsultasController@cosultaMagento']);


//PARTE ADMINISTRADOR
$router->get('/consultarImagenes', ['uses' => 'ConsultasController@consultarImagenes']);
$router->get('/'.env('URL_IMAGENES').'{id_tipo_archivo}/{nombre_archivo}', 'ConsultasController@ConsultarArchivo');

$router->get('/consultarGarantiasDisponibles', ['uses' => 'ConsultasController@consultarGarantiasDisponibles']);

//PARTE MAGENTO
$router->get('/homeFesa', ['uses' => 'ConsultasController@consultarImagenesMagento']);
$router->get('/'.env('URL_IMAGENES').'{id_tipo_archivo}/{nombre_archivo}', 'ConsultasController@ConsultarArchivoMa');




//$router->get('/admin', function(){
 //   return view('includes.dash');
//});

//$router->get('/producto/categoria', 'ProductoController@categoria');

//$router->get('/user/saludar', 'UserController@saludo');

