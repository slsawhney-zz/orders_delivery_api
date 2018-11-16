<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Models\Order;
use App\Helper\ApiHelper;

class ApiController
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, $args = [])
    {
        return $response->withJson($request->getQueryParams());
    }

    /**
     * Orders Function to list orders.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param int               $args
     */
    public function orders(RequestInterface $request, ResponseInterface $response, $args)
    {
        $page = $args['page'] ?? getenv('START');
        $limit = $args['limit'] ?? getenv('LIMIT');

        $startFrom = ($page == 1) ? 0 : (($page - 1) * getenv('LIMIT'));

        $order = new Order();
        $orders = $order->getOrders($startFrom, $limit);

        $responseBody = $response->getBody();
        $responseBody->write(json_encode($orders));

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200)
            ->withBody($responseBody);
    }

    /**
     * Function to save new order.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param int               $args
     */
    public function saveOrder(RequestInterface $request, ResponseInterface $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $apiHelper = new ApiHelper();
        $validationResponse = $apiHelper->verifyRequiredParams(array('origin', 'destination'), $parsedBody);

        $information = $params = [];
        $params = [
             'start_latitude' => $parsedBody['origin']['0'],
             'start_longitude' => $parsedBody['origin']['1'],
             'end_latitude' => $parsedBody['destination']['0'],
             'end_longitude' => $parsedBody['destination']['1'],
        ];

        $flag = $apiHelper->validateLatitudeLongitude($params);
        $statusCode = 200;

        if ($flag) {
            $order = new Order();
            $result = $order->createOrder($params);
            if ($result === 0) {
                $information = ['message' => 'Order not created successfully'];
                $statusCode = 500;
            } else {
                $information = [
                    'id' => $result['id'],
                    'distance' => $result['distance'],
                    'status' => $result['status'],
                ];
            }
        } else {
            $information = ['error' => 'Entered data is not valid'];
            $statusCode = 400;
        }

        $responseBody = $response->getBody();
        $responseBody->write(json_encode($information));

        return $response->withHeader('Content-Type', 'application/json')
                ->withStatus($statusCode)
                ->withBody($responseBody);
    }

    /**
     * function to update order.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param int               $args
     */
    public function updateOrder(RequestInterface $request, ResponseInterface $response, $args)
    {
        $postMethod = $request->getMethod();
        $parsedBody = $request->getParsedBody();
        $orderID = $args['id'];

        $apiHelper = new ApiHelper();
        $request_params = $apiHelper->verifyRequiredParams(array('status'), $postMethod, $parsedBody);

        $information = ['error' => 'Entered Body or OrderId is not valid'];
        $statusCode = 404;

        if ($parsedBody['status'] === 'TAKEN' && is_numeric($orderID)) {
            $order = new Order();
            $result = $order->updateOrder($orderID);
            $statusCode = 200;

            $information = [];

            switch ($result) {
                case 0:
                    $information = ['error' => 'ORDER_ALREADY_BEEN_TAKEN'];
                    $statusCode = 409;
                    break;
                case 1:
                    $information = ['status' => 'SUCCESS'];
                    break;
                case 2:
                    $information = ['error' => 'INVALID_ORDER_ID'];
                    $statusCode = 404;
                    break;
                default:
                    $information = ['error' => 'ERROR_OCCURED'];
                    $statusCode = 500;
            }
        }

        $responseBody = $response->getBody();
        $responseBody->write(json_encode($information));

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode)
            ->withBody($responseBody);
    }
}
