<?php

require_once 'vendor/autoload.php';

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Tester\Exception\PendingException;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use SeanUk\Silex\Stack\Stack;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Silex\Provider\ServiceControllerServiceProvider;
use SeanUk\Silex\Controller\StackController;


/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var Application $app */
    private $app;

    /** @var Response $last_response */
    private $last_response;

    /** @var Stack $stack */
    private $stack;

    /** @var string $stack_path */
    private $stack_path;

    /** @var Serializer $serializer */
    private $serializer;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        // vfsStrem for FS mocking: {@see https://github.com/mikey179/vfsStream/wiki/Tutorial}
        vfsStream::setup('root');
        $this->stack_path = vfsStream::url('root/stack.yml');

        // construct a yaml serializer
        $encoders = [new YamlEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);

        $this->stack = new Stack($this->stack_path, $this->serializer, 'yaml');

        // build an app to test
        $this->iStartANewSession();
    }

    /**
     * @Given I post an empty JSON object
     */
    public function iPostAnEmptyJsonObject()
    {
        $string = '{}';
        $this->iPostTheJsonObject(new PyStringNode([$string], 1));
    }

    /**
     * @Given I post the JSON object :json
     */
    public function iPostTheJsonObject($json)
    {
        $jsonString = (string)$json;

        // construct a request. usage based on \Symfony\Component\HttpFoundation\Tests\RequestTest::testCreate
        /** @var Request $request */
        $request = Request::create('/push', 'POST', [], [], [], [], $jsonString);

        $this->last_response = $this->app->handle($request);
    }

    /**
     * @Then the response success should be true
     */
    public function theResponseSuccessShouldBeTrue()
    {
        Assert::assertInstanceOf(JsonResponse::class, $this->last_response);
        Assert::assertEquals(Response::HTTP_OK, $this->last_response->getStatusCode());

        // decode and check the json response
        /** @var JsonResponse $response */
        $json = $this->last_response->getContent();
        $data = json_decode($json, true);
        Assert::assertArrayHasKey('success', $data);
        Assert::assertEquals(true, $data['success']);
    }

    /**
     * @Then the response error message should be empty
     */
    public function theResposeErrorMessageShouldBeEmpty()
    {
        Assert::assertInstanceOf(JsonResponse::class, $this->last_response);

        $json = $this->last_response->getContent();
        $data = json_decode($json, true);

        Assert::assertArrayHasKey('message', $data);
        Assert::assertEmpty($data['message']);
    }

    /**
     * @Then the response success should be false
     */
    public function theResponseSuccessShouldBeFalse()
    {
        Assert::assertInstanceOf(JsonResponse::class, $this->last_response);
        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $this->last_response->getStatusCode());

        // decode and check the json response
        $json = $this->last_response->getContent();
        $data = json_decode($json, true);
        Assert::assertArrayHasKey('success', $data);
        Assert::assertEquals(false, $data['success']);
    }

    /**
     * @Then the response error message should not be empty
     */
    public function theResponseErrorMessageShouldNotBeEmpty()
    {
        Assert::assertInstanceOf(JsonResponse::class, $this->last_response);

        $json = $this->last_response->getContent();
        $data = json_decode($json, true);

        Assert::assertArrayHasKey('message', $data);
        Assert::assertNotEmpty($data['message']);
    }

    /**
     * @Given the stack is empty
     */
    public function theStackIsEmpty()
    {
        $this->stack->flush();
    }

    /**
     * @When I pop the stack
     */
    public function iPopTheStack()
    {
        $request = Request::create('/pop');
        $this->last_response = $this->app->handle($request);
    }

    /**
     * @Then I should get a null response
     */
    public function iShouldGetANullResponse()
    {
        Assert::assertInstanceOf(JsonResponse::class, $this->last_response);
        Assert::assertEquals('null', $this->last_response->getContent());
    }

    /**
     * @Given this has been pushed onto the stack: :json
     */
    public function thisHasBeenPushedOntoTheStack($json)
    {
        // bypass the app, just setup the stack direct
        $json = (string) $json;
        $this->stack->push($json);
    }

    /**
     * @Then the response JSON should be: :expectedJson
     */
    public function theResponseJsonShouldBe($expectedJson)
    {
        $expectedJson = (string) $expectedJson;

        // get the response json
        Assert::assertInstanceOf(JsonResponse::class, $this->last_response);
        $responseJson = $this->last_response->getContent();

        // compare them as data structures rather than strings
        $expectedData = json_decode($expectedJson, true);
        $responseData = json_decode($responseJson, true);
        Assert::assertEquals($expectedData, $responseData);
    }

    /**
     * @When I start a new session
     */
    public function iStartANewSession()
    {
        $this->app = new Application();

        $app = $this->app;
        $app->register(new ServiceControllerServiceProvider());
        $this->stack = new Stack($this->stack_path, $this->serializer, 'yaml');
        $stack = $this->stack;
        $app['stack.controller'] = function () use ($stack) {
            return new StackController($stack);
        };
        // configure stack routes
        $app->post('push', "stack.controller:pushAction");
        $app->get('pop', "stack.controller:popAction");
    }
}