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
use Symfony\Component\HttpFoundation\Request;
use SeanUk\Silex\App\StackApp;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Silex\Provider\VarDumperServiceProvider;

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

// doctrine orm service
// setup: {@see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/getting-started.html}
// service creation: {@see https://silex.symfony.com/doc/2.0/services.html}
$app['entity_manager'] = function () {
    // 'useSimpleAnnotationReader' should be false: https://stackoverflow.com/a/19129147
    $metadataConfig = Setup::createAnnotationMetadataConfiguration([__DIR__.'/src/Entity'], true, null, null, false);
    $dbConfig = [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__.'/data/stack.sqlite',
    ];
    $em = EntityManager::create($dbConfig, $metadataConfig);
    return $em;
};

$stack = new Stack();
StackApp::build($app, $stack);