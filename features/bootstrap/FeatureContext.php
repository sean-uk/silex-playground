<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Tester\Exception\PendingException;
use SeanUk\Silex\App\StackApp;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

require_once 'vendor/autoload.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var StackApp */
    private $app;

    /** @var Response $last_response */
    private $last_response;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->app = StackApp::create();
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
     * @Given I post the JSON object
     * @param PyStringNode $json
     */
    public function iPostTheJsonObject(PyStringNode $json)
    {
        $jsonString = (string) $json;

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
}
