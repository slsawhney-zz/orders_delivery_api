<?php

namespace App\Test\Feature\ApiController;

class OrderControllerTest extends \PHPUnit\Framework\TestCase
{
    protected $client;

    protected function guzzleObject($base_uri, $header)
    {
        return $this->client = new \GuzzleHttp\Client([
            'base_uri' => $base_uri,
            'headers' => $header,
        ]);
    }

    protected function setUp()
    {
        $theHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $this->guzzleObject('http://nginx', $theHeaders);
    }

    public function testOrdersIntegrations()
    {
        echo "\n \n ---------Starts Executing API Integration Test--------- \n \n";

        echo "\n \n ---------Creating orders (Empty Origin)--------- \n \n";

        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => [],
                'destination' => [
                    '28.535517',
                    '77.391029',
                ],
            ],
        ]);

        $this->assertEquals(406, $response->getStatusCode());

        echo "\n \n ---------Creating orders (Empty Destination)--------- \n \n";

        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => [
                    '28.704060',
                    '77.102493',
                ],
                'destination' => [],
            ],
        ]);
        $this->assertEquals(406, $response->getStatusCode());

        echo "\n \n ---------Creating orders--------- \n \n";

        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => [
                    '28.704060',
                    '77.102493',
                ],
                'destination' => [
                    '28.535517',
                    '77.391029',
                ],
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('distance', $data);

        echo "\n \n ---------Updating order--------- \n \n";

        $response = $this->client->patch('/orders/'.$data['id'], [
            'json' => [
                'status' => 'TAKEN',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n \n ---------Updating order (Invalid Order ID)--------- \n \n";

        $response = $this->client->patch('/orders/10000', [
            'json' => [
                'status' => 'TAKEN',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(406, $response->getStatusCode());

        echo "\n \n ---------Fetching orders--------- \n \n";
        $response = $this->client->get('/orders', [
            'query' => [
                'page' => 1,
                'limit' => 10,
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody()->getContents(), true);

        foreach ($data as $order) {
            $this->assertArrayHasKey('id', $order);
            $this->assertArrayHasKey('status', $order);
            $this->assertArrayHasKey('distance', $order);
        }

        echo "\n \n ---------API Integration Test Execution Finished ---------\n \n";
    }
}
