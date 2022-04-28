<?php
/**
 * Created by PhpStorm.
 * User: Rafael Freitas
 * Date: 25/05/2019
 * Time: 11:16
 */

//ini_set('display_errors',0);
//ini_set('display_startup_erros',0);
//error_reporting(0);
//error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);

//Groups
// 1 -> Admin
// 2 -> Avaliador
// 3 -> Cliente

define('UPLOAD_CLI', __DIR__ . '/upload_api/files/');

require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Config/Slim.php';
require __DIR__ . '/src/Groups/AuthGroup.php';
require __DIR__ . '/src/Groups/CompaniesGroup.php';
require __DIR__ . '/src/Groups/CompanyUserGroup.php';
require __DIR__ . '/src/Groups/EvaluationGroup.php';
require __DIR__ . '/src/Groups/EvaluatorsGroup.php';
require __DIR__ . '/src/Groups/GraphicsGroup.php';
require __DIR__ . '/src/Groups/LogGroup.php';
require __DIR__ . '/src/Groups/RN452Group.php';
require __DIR__ . '/src/Groups/RN440Group.php';
require __DIR__ . '/src/Groups/UserAdminGroup.php';

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler;
    return $handler($req, $res);
});

$app->run();