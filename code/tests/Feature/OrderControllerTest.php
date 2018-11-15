<?php

namespace App\Test\Feature\ApiController;

class OrderControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testOrdersIntegrations()
    {
        echo "---------\n \n Starts Executing API Integration Test \n \n---------";

        // instantiate action
        $action = new \App\Controller\ApiController();

        echo "---------\n \n Creating orders \n \n---------";
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

        echo "---------\n \n Fetching orders \n \n---------";
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/orders/1/2',
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        // run the controller action and test it
        $response = $action($request, $response, []);
        $this->assertSame((string) $response->getBody(), '[]');

        echo "---------\n \n Updating order \n \n---------";
        $randId = rand(2, 40);
        $environment = \Slim\Http\Environment::mock([
            'REQUEST_METHOD' => 'PATCH',
            'REQUEST_URI' => '/order/'.$randId,
        ]);
        $request = \Slim\Http\Request::createFromEnvironment($environment);
        $response = new \Slim\Http\Response();
        $response = $action($request, $response, ['status' => 'TAKEN']);
        $this->assertSame((string) $response->getBody(), '[]');

        echo "---------\n \n API Integration Test Execution Finished \n \n---------";
    }
}
