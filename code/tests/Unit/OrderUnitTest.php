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
            'REQUEST_URI' => '/orders',
            'QUERY_STRING' => 'page=1&limit=3',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();

        $response = $action($request, $response, []);

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n \n GET Orders test case passed \n \n";
    }

    public function testOrdersNoDataFound()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/orders',
            'QUERY_STRING' => 'page=10000&limit=3',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, []);

        $this->assertEquals(200, $response->getStatusCode());

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
            'REQUEST_URI' => '/orders',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, $body);

        $this->assertEquals(200, $response->getStatusCode());
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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());

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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());

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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);

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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);

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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);

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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);

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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);
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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);
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
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->saveOrder($request, $response, $body);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);
        echo "\n \n Create Order Invalid longitude range test case passed \n \n";
    }

    public function testUpdateOrderMissingRequestBody()
    {
        $action = new \App\Controller\ApiController();
        $body = '';
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
        ]);

        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->updateOrder($request, $response, ['id' => '1']);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);
        echo "\n \n Update Order missing parameter test case passed \n \n";
    }

    public function testUpdateOrderInvalidId()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->updateOrder($request, $response, ['id' => '10d', 'status' => 'TAKEN']);

        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);
        echo "\n \n Update Order invalid order id test case passed \n \n";
    }

    public function testUpdateOrderOrderAlreadyTaken()
    {
        $action = new \App\Controller\ApiController();
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action->updateOrder($request, $response, ['id' => '1', 'status' => 'TAKEN']);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $data);
        echo "\n \n Update Order order already taken test case passed \n \n";

        echo "\n \n Unit Test Cases Execution Finished \n \n";
    }
}
