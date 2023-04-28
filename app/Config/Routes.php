<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * CRUD RESTful Routes
 * --------------------------------------------------------------------
 */

 //Usuario
 $routes->get('usuario', 'Usuario::getIndex');
 $routes->post('usuario', 'Usuario::postCreate');
 $routes->put('usuario', 'Usuario::putUpdate');
 $routes->options('usuario', 'Usuario::optionsIndex');
 $routes->get('usuario/delete/(:num)', 'Usuario::deleteDelete/$1');
 $routes->options('usuario/delete/(:num)', 'Usuario::optionsIndex');
 $routes->post('login', 'Usuario::postLogin');
 $routes->options('login', 'Usuario::optionsIndex');
 $routes->post('logout', 'Usuario::postLogout');
 $routes->options('logout', 'Usuario::optionsIndex');

 //Producto
 $routes->get('producto', 'Producto::getIndex');
 $routes->post('producto', 'Producto::postCreate');
 $routes->put('producto', 'Producto::putUpdate');
 $routes->options('producto', 'Producto::optionsIndex');
 $routes->get('producto/delete/(:num)', 'Producto::deleteDelete/$1');
 $routes->options('producto/delete/(:num)', 'Producto::optionsIndex');
 $routes->get('producto/(:num)', 'Producto::getPorId/$1');
 $routes->options('producto/(:num)', 'Producto::optionsIndex');
 $routes->get('producto/getPorUsuario/(:num)', 'Producto::getPorUsuario/$1');
 $routes->options('producto/getPorUsuario/(:num)', 'Producto::optionsIndex');

 //Compra
 $routes->get('compra', 'Compra::getIndex');
 $routes->post('compra', 'Compra::postCreate');
 $routes->put('compra', 'Compra::putUpdate');
 $routes->options('compra', 'Compra::optionsIndex');
 $routes->get('compra/delete/(:num)', 'Compra::deleteDelete/$1');
 $routes->options('compra/delete/(:num)', 'Compra::optionsIndex');
 $routes->get('compra/(:num)', 'Compra::getPorId/$1');
 $routes->options('compra/(:num)', 'Compra::optionsIndex');

 //Venta
 $routes->get('venta', 'Venta::getIndex');
 $routes->post('venta', 'Venta::postCreate');
 $routes->put('venta', 'Venta::putUpdate');
 $routes->options('venta', 'Venta::optionsIndex');
 $routes->get('venta/delete/(:num)', 'Venta::deleteDelete/$1');
 $routes->options('venta/delete/(:num)', 'Venta::optionsIndex');
 $routes->get('venta/(:num)', 'Venta::getPorId/$1');
 $routes->options('venta/(:num)', 'Venta::optionsIndex');

 //CompraProducto
 $routes->get('compra_producto', 'CompraProducto::getIndex');
 $routes->post('compra_producto', 'CompraProducto::postCreate');
 $routes->put('compra_producto', 'CompraProducto::putUpdate');
 $routes->options('compra_producto', 'CompraProducto::optionsIndex');
 $routes->get('compra_producto/delete/(:num)', 'CompraProducto::deleteDelete/$1');
 $routes->options('compra_producto/delete/(:num)', 'CompraProducto::optionsIndex'); 
 $routes->get('compra_producto/getPorUsuario/(:num)', 'CompraProducto::getPorUsuario/$1');
 $routes->options('compra_producto/getPorUsuario/(:num)', 'CompraProducto::optionsIndex');

 //VentaProducto
 $routes->get('venta_producto', 'VentaProducto::getIndex');
 $routes->post('venta_producto', 'VentaProducto::postCreate');
 $routes->put('venta_producto', 'VentaProducto::putUpdate');
 $routes->options('venta_producto', 'VentaProducto::optionsIndex');
 $routes->get('venta_producto/delete/(:num)', 'VentaProducto::deleteDelete/$1');
 $routes->options('venta_producto/delete/(:num)', 'VentaProducto::optionsIndex');
 $routes->get('venta_producto/getPorUsuario/(:num)', 'VentaProducto::getPorUsuario/$1');
 $routes->options('venta_producto/getPorUsuario/(:num)', 'VentaProducto::optionsIndex');
 $routes->get('venta_producto/getPorUsuarioPorProducto/(:num)', 'VentaProducto::getPorUsuarioPorProducto/$1');
 $routes->options('venta_producto/getPorUsuarioPorProducto/(:num)', 'VentaProducto::optionsIndex');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
