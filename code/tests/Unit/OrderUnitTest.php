<?php

namespace App\Test\Unit\ApiController;

class ApiControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testOrders()
    {
        echo "\n \n Starts Executing Unit Test Cases \n \n";
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/orders/1/2',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');

        echo "\n \n GET Orders test case passed \n \n";
    }

    public function testOrdersRequestParamMisiing()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/orders',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n GET Orders without parameters test case passed \n \n";
    }

    public function testOrdersRequestParamTypeInvalid()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/orders/page/2',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n GET Orders with invalid parameter type test case passed \n \n";
    }

    public function testOrdersNoDataFound()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/orders/10000/2',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');

        echo "\n \n GET Orders no data found test case passed \n \n";
    }

    public function testSaveOrder()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '28.704060',
                '77.102493',
            ],
            'destination' => [
                '28.535517',
                '77.391029',
            ], }";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order test case passed \n \n";
    }

    public function testSaveOrderOriginDestinationDuplicate()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '28.704060',
                '77.102493',
            ],
            'destination' => [
                '28.704060',
                '77.102493',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');

        echo "\n \n Create Order duplicate origin and destination test case passed \n \n";
    }

    public function testSaveOrderMissingOriginRequest()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'destination' => [
                '28.704060',
                '77.102493',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order missing origin test case passed \n \n";
    }

    public function testSaveOrderMissingDestinationRequest()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '28.704060',
                '77.102493',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order missing destination test case passed \n \n";
    }

    public function testSaveOrderStartLatitudeMissing()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '',
                '77.102493',
            ],
            'destination' => [
                '28.535517',
                '77.391029',
            ]}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order start lattitude missing test case passed \n \n";
    }

    public function testSaveOrderStartLongitudeMissing()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '28.704060',
                '',
            ],
            'destination' => [
                '28.535517',
                '77.391029',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order start longitude missing test case passed \n \n";
    }

    public function testSaveOrderEndLatitudeMissing()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '28.704060',
                '77.102493',
            ],
            'destination' => [
                '',
                '77.391029',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order end latitude missing test case passed \n \n";
    }

    public function testSaveOrderEndLongitudeMissing()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '28.704060',
                '77.102493',
            ],
            'destination' => [
                '28.535517',
                '',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order end longitude missing test case passed \n \n";
    }

    public function testSaveOrderInvalidLatitudeRange()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '98.704060',
                '77.102493',
            ],
            'destination' => [
                '28.535517',
                '77.391029',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order Invalid latitude range test case passed \n \n";
    }

    public function testSaveOrderInvalidLonitudeRange()
    {
        $action = new \App\Controller\ApiController();
        $body = "{
            'origin' => [
                '28.704060',
                '77.102493',
            ],
            'destination' => [
                '28.535517',
                '197.391029',
            ],}";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Create Order Invalid longitude range test case passed \n \n";
    }

    public function testUpdateOrder()
    {
        $randOrderId = rand(2, 40);
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
            'REQUEST_URI' => '/order/'.$randOrderId,
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, ['status' => 'TAKEN']);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Update Order test case passed \n \n";
    }

    public function testUpdateOrderMissingRequestBody()
    {
        $action = new \App\Controller\ApiController();
        $body = '';
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
            'REQUEST_URI' => '/order',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $body = new \Slim\Http\RequestBody();
        $body->write($body);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Update Order missing parameter test case passed \n \n";
    }

    public function testUpdateOrderInvalidId()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
            'REQUEST_URI' => '/order/10d',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, ['status' => 'TAKEN']);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Update Order invalid order id test case passed \n \n";
    }

    public function testUpdateOrderOrderAlreadyTaken()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
            'REQUEST_URI' => '/order/1',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, ['status' => 'TAKEN']);
        $this->assertSame((string) $response->getBody(), '[]');
        echo "\n \n Update Order order already taken test case passed \n \n";

        echo "\n \n Unit Test Cases Execution Finished \n \n";
    }
}
