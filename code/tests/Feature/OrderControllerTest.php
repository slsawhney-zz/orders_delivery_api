<?php

namespace Tests\Feature;

use Slim\Http\Environment;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;

class ApiControllerTest extends TestCase
{
    public function testOrdersApiIntegration()
    {
        echo "\n \n Starts Executing API Integration Test \n \n";

        $createResponse = $this->json('POST', '/order', [
            "origin" => [
                "28.704060",
                "77.102493"
            ],
            "destination" => [
                "28.535517",
                "77.391029"
            ]
        ]);
        $createResponse->assertStatus(200);
        echo "\n \n Creating order \n \n";

        $getResponse = $this->json('GET', '/orders/1/10');
        $getResponse->assertStatus(200);
        echo "\n \n Fetching orders \n \n";

        $randId = rand(2,40);
        $updateResponse = $this->json('PATCH', '/order/'.$randId, ["status" => "TAKEN"]);
        $updateResponse->assertStatus(200);
        echo "\n \n Updating order \n \n";
        
        echo "\n \n API Integration Test Execution Finished \n \n";
    }
}
