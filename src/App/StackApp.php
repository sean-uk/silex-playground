<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 18/08/17
 * Time: 17:54
 */

namespace SeanUk\Silex\App;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StackApp
{
    /**
     * @return Application
     */
    public static function create()
    {
        $app = new Application();
        $app->post('push', function (Request $request) {
            return new JsonResponse(['success'=>false, 'message'=>'it broke. sorry.']);
        });

        return $app;
    }
}