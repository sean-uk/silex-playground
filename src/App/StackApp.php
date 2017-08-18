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
use Symfony\Component\HttpFoundation\Response;

class StackApp
{
    /**
     * @return Application
     */
    public static function create()
    {
        $app = new Application();
        $app->post('push', function (Request $request) {
            $content = $request->getContent();
            if (!json_decode($content)) {
                return new JsonResponse(['success'=>false, 'message'=>'invalid content.'], Response::HTTP_BAD_REQUEST);
            }
            return new JsonResponse(['success'=>true, 'message'=>'']);
        });

        return $app;
    }
}