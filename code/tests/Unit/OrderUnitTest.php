<?php
namespace Tests\Unit;

use Slim\Http\Environment;
use Slim\Http\Request;

class ApiControllerTest extends PHPUnit_Framework_TestCase
{
    use WithoutMiddleware;
    public function testOrders()
    {
        echo "\n \n Starts Executing Unit Test Cases \n \n";
        $response = $this->json('GET', '/orders/1/10');
        $response->assertStatus(200);
        echo "\n \n GET Orders test case passed \n \n";
    }
    public function testOrdersRequestParamMisiing()
    {
        $response = $this->json('GET', '/orders');
        $response->assertStatus(406);
        echo "\n \n GET Orders without parameters test case passed \n \n";
    }
    public function testOrdersRequestParamTypeInvalid()
    {
        $response = $this->json('GET', '/orders/abc/10');
        $response->assertStatus(406);
        echo "\n \n GET Orders with invalid parameter type test case passed \n \n";
    }
    public function testOrdersNoDataFound()
    {
        $response = $this->json('GET', '/orders/10001/10');
        $response->assertStatus(204);
        echo "\n \n GET Orders no data found test case passed \n \n";
    }
    public function testSaveOrder()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);
        $response->assertStatus(200);
        echo "\n \n Create Order test case passed \n \n";
    }
    public function testSaveOrderOriginDestinationDuplicate()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.704060",
                "77.102493"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order duplicate origin and destination test case passed \n \n";
    }
    public function testSaveOrderMissingOriginRequest()
    {
        $response = $this->json('POST', '/order', [
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order missing origin test case passed \n \n";
    }
    public function testSaveOrderMissingDestinationRequest()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                "77.102493"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order missing destination test case passed \n \n";
    }
    public function testSaveOrderStartLatitudeMissing()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order start lattitude missing test case passed \n \n";
    }
    public function testSaveOrderStartLongitudeMissing()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                ""
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order start longitude missing test case passed \n \n";
    }
    public function testSaveOrderEndLatitudeMissing()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "",
                "77.391029"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order end latitude missing test case passed \n \n";
    }
    public function testSaveOrderEndLongitudeMissing()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                ""
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order end longitude missing test case passed \n \n";
    }
    public function testSaveOrderInvalidLatitudeRange()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "98.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order Invalid latitude range test case passed \n \n";
    }
    public function testSaveOrderInvalidLonitudeRange()
    {
        $response = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "197.391029"
            ]
        ]);
        $response->assertStatus(406);
        echo "\n \n Create Order Invalid longitude range test case passed \n \n";
    }
    public function testUpdateOrder()
    {
        $randOrderId = rand(2,40);
        $response = $this->json('PATCH', '/order/'.$randOrderId, [
                "status" => "TAKEN"
        ]);
        $response->assertStatus(200);
        echo "\n \n Update Order test case passed \n \n";
    }
    public function testUpdateOrderMissingRequestBody()
    {
        $response = $this->json('PATCH', '/order/1', []);
        $response->assertStatus(406);
        echo "\n \n Update Order missing parameter test case passed \n \n";
    }
    public function testUpdateOrderInvalidId()
    {
        $response = $this->json('PATCH', '/order/101a', []);
        $response->assertStatus(406);
        echo "\n \n Update Order invalid order id test case passed \n \n";
    }
    public function testUpdateOrderOrderAlreadyTaken()
    {
        $response = $this->json('PATCH', '/order/1', [
                "status" => "TAKEN"
        ]);
        $response->assertStatus(409);
        echo "\n \n Update Order order already taken test case passed \n \n";
        
        echo "\n \n Unit Test Cases Execution Finished \n \n";
    }
}
