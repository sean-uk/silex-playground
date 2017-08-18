<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 15/08/17
 * Time: 20:50
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

// bootstrap silex
$app = new Application();
$app['debug'] = true;

$app->get('/', function () {
    return new Response('HELLO WORLD!');
});

$app->get('reverse/{string}', function ($string) {
    $reversed = strrev($string);
    return new Response($reversed);
});

$app->run();