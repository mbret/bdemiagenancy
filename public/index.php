<?php

setlocale(LC_TIME, 'fr_FR', 'fra');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('DATA_PATH')
    || define('DATA_PATH', realpath(dirname(__FILE__) . '/../data'));

defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../public'));

defined('PUBLIC_FILES_PATH')
    || define('PUBLIC_FILES_PATH', PUBLIC_PATH . '/files');

defined('PUBLIC_RESOURCES_PATH')
    || define('PUBLIC_RESOURCES_PATH', PUBLIC_PATH . '/resources');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library', // Chemin relatif (optimisation)
    get_include_path(),
)));

// Utilisation de l'autoloader (Zend_, ZendX_)
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance(); // Charge toutes les classes Zend

// Start session here instead of bootstrap
Zend_Session::start();

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    array(
        'config' => array(
            APPLICATION_PATH . '/configs/application.ini', // add application.ini in the pluginresource loader
            APPLICATION_PATH . '/configs/db.ini', // add db.ini in the pluginresource loader
            APPLICATION_PATH . '/configs/routes.ini', // add configuration ressources for router
        )
    )
);
$application->bootstrap()
            ->run();