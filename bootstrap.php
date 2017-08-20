<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 19/08/17
 * Time: 22:58
 */
require_once __DIR__ . '/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use SeanUk\Silex\Stack\Stack;
use Silex\Provider\VarDumperServiceProvider;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use SeanUk\Silex\Controller\StackController;
use Silex\Provider\ServiceControllerServiceProvider;

// bootstrap silex
$app = new Application();
$app['debug'] = true;
$app->register(new VarDumperServiceProvider());

$app->get('', function () {
    return new Response('HELLO WORLD!');
});

$app->get('reverse/{string}', function ($string) {
    $reversed = strrev($string);
    return new Response($reversed);
});

// persistence service and storage param
$app['stack.path'] = __DIR__.'/data/stack.yml';

// configure a YAML serializer - {@see https://symfony.com/doc/current/components/serializer.html}
// service creation: {@see https://silex.symfony.com/doc/2.0/services.html}
$app['stack.serializer'] = function () {
    $encoders = [new YamlEncoder()];
    $normalizers = [new ObjectNormalizer()];
    $serializer = new Serializer($normalizers, $encoders);
    return $serializer;
};

// configure the stack controller - {@see https://silex.symfony.com/doc/2.0/providers/service_controller.html#usage}
$app->register(new ServiceControllerServiceProvider());
$app['stack.controller'] = function () use ($app) {
    $stack = new Stack($app['stack.path'], $app['stack.serializer'], 'yaml');
    return new StackController($stack);
};

// configure stack routes
$app->post('push', "stack.controller:pushAction");
$app->get('pop', "stack.controller:popAction");