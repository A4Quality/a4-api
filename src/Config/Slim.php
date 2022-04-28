<?php

date_default_timezone_set('America/Recife');
//header("Content-Type: application/json");

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App([
    'templates.path' => 'templates',
    'settings' => ['displayErrorDetails' => true],
]);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader(
            'Access-Control-Allow-Headers',
            'X-Requested-With, Content-Type, Accept, Origin, Authorization'
        )
        ->withHeader(
            'Access-Control-Allow-Methods',
            'GET, POST, PUT, DELETE, OPTIONS'
        )
        ->withHeader('Access-Control-Expose-Headers', 'Authorization');
});

$app->get('/', function (Request $request, Response $response, $args) {
    return $response->withJson([
        'status' => 200,
        'message' => "Api Manager A4Quality",
    ]);
});
