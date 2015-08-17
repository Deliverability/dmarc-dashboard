<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;



/**
 * Test for access
 */
$allowAccess = false;
if (file_exists(__DIR__.'/../dev-hosts.php')) {
    $result = require(__DIR__.'/../dev-hosts.php');
    if ($result === true) {
        $allowAccess = true;
    }
}
if ($allowAccess !== true) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}



/**
 * Load application
 */
$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
