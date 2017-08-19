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
use SeanUk\Silex\Stack\Stack;

/**
 * @todo use a proper controller instead of this thing
 */
class StackApp
{
    /**
     * Add StackApp routes to silex app
     *
     * @param Application $app
     * @param Stack $stack
     */
    public static function build(Application $app, Stack $stack)
    {
        // stack push
        $app->post('push', function (Request $request) use ($stack) {
            $content = $request->getContent();
            $pushed = $stack->push($content);
            if ($pushed) {
                return new JsonResponse(['success'=>true, 'message'=>'']);
            }
            return new JsonResponse(['success'=>false, 'message'=>'invalid content.'], Response::HTTP_BAD_REQUEST);
        });

        // stack pop
        $app->get('pop', function () use ($stack) {
            // stack is empty? return null
            if ($stack->isEmpty()) {
                return new JsonResponse('null', Response::HTTP_BAD_REQUEST, [], true);
            }

            $record = $stack->pop();
            return new JsonResponse($record, Response::HTTP_OK, [], true);
        });
    }
}