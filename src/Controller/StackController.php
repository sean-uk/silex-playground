<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 20/08/17
 * Time: 16:19
 */

namespace SeanUk\Silex\Controller;

use SeanUk\Silex\Stack\Stack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class StackController
{
    /** @var Stack $stack */
    private $stack;

    public function __construct(Stack $stack)
    {
        $this->stack = $stack;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function pushAction(Request $request)
    {
        $content = $request->getContent();
        $pushed = $this->stack->push($content);
        if ($pushed) {
            return new JsonResponse(['success'=>true, 'message'=>'']);
        }
        return new JsonResponse(['success'=>false, 'message'=>'invalid content.'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @return JsonResponse
     */
    public function popAction()
    {
        // stack is empty? return null
        if ($this->stack->isEmpty()) {
            return new JsonResponse('null', Response::HTTP_BAD_REQUEST, [], true);
        }

        $record = $this->stack->pop();
        return new JsonResponse($record, Response::HTTP_OK, [], true);
    }
}