<?php
/**
 * Doctrine ORM cli config.
 *
 * Created by PhpStorm.
 * User: sean
 * Date: 19/08/17
 * Time: 22:39
 */

require_once __DIR__.'/../bootstrap.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $app['entity_manager'];

return ConsoleRunner::createHelperSet($entityManager);