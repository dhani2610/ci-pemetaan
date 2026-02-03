<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

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
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('create-db', function(){
    $forge = \Config\Database::forge();
    if ($forge->createDatabase('db_ci4')) {
        echo 'Database created!';
    }
});

//route login
$routes->get('login', 'Auth::login');
$routes->get('admin-login', 'Auth::adminLogin');
$routes->get('register', 'Auth::register');
$routes->post('auth/registerProcess', 'Auth::registerProcess');

// $routes->get('/', 'Home::index');
$routes->addRedirect('/', 'peta');

$routes->get('admin', 'Admin::index');
$routes->post('admin/store', 'Admin::store');
$routes->put('admin/update', 'Admin::update'); // Menggunakan PUT untuk update
$routes->delete('admin/(:num)', 'Admin::destroy/$1'); // Menggunakan DELETE untuk hapus


$routes->get('umkm', 'Umkm::index');
$routes->post('umkm/store', 'Umkm::store');
$routes->put('umkm/update', 'Umkm::update');
$routes->delete('umkm/(:num)', 'Umkm::destroy/$1');

$routes->get('outlet', 'Outlet::index');
$routes->post('outlet/store', 'Outlet::store');
$routes->post('outlet/update', 'Outlet::update'); // Pakai POST karena ada upload file
$routes->delete('outlet/(:num)', 'Outlet::destroy/$1');
$routes->get('outlet/getData/(:num)', 'Outlet::getData/$1');
$routes->post('outlet/approve', 'Outlet::approve');


$routes->get('produk', 'Produk::index');
$routes->post('produk/store', 'Produk::store');
$routes->put('produk/update', 'Produk::update');
$routes->delete('produk/(:num)', 'Produk::destroy/$1');

$routes->get('peta', 'Peta::index');
$routes->get('peta/api/outlets', 'Peta::getOutlets'); // API untuk ambil titik koordinat
$routes->get('peta/api/detail/(:num)', 'Peta::getDetail/$1'); // API untuk detail modal
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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
