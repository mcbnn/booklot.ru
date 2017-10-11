<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

chdir(dirname(__DIR__));
global $site;
global $microtime;
$microtime = microtime(true);
$site = 'www.booklot.ru';
//if(isset($_GET['bug'])){
//	die();
//
//	if(mb_strstr($_SERVER['REQUEST_URI'],'page-', 'UTF-8')){
//		$uri = str_replace('page-','',$_SERVER['REQUEST_URI']);
//		header("HTTP/1.1 301 Moved Permanently");
//		header("Location: http://".$site.$uri);
//		exit(); 
//		var_dump($uri);
//	die();
//	}
//
//
//}
// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}
ini_set('display_errors',0);
// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
