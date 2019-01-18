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
        $page = $request->getQueryParam('page');
        $limit = $request->getQueryParam('limit');

        $apiHelper = new ApiHelper();
        $pageLimitCheck = $apiHelper->pageLimitCheck($page, $limit);

        if (empty($pageLimitCheck)) {
            $offset = ($page - 1) * $limit;

            $order = new Order();
            $information = $order->getOrders($offset, $limit);

            $statusCode = !empty($information) ? 200 : 404;
        } else {
            $information = ['error' => $pageLimitCheck['error']];
            $statusCode = 404;
        }

        $responseBody = $response->getBody();
        $responseBody->write(json_encode($information));

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode)
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

        $latitudeLongitudeCountFlag = $apiHelper->validateLatitudeLongitudeCount($parsedBody);

        if ($latitudeLongitudeCountFlag && empty($validationResponse)) {
            $params = [
                 'start_latitude' => isset($parsedBody['origin']['0']) ? $parsedBody['origin']['0'] : '',
                 'start_longitude' => isset($parsedBody['origin']['1']) ? $parsedBody['origin']['1'] : '',
                 'end_latitude' => isset($parsedBody['destination']['0']) ? $parsedBody['destination']['0'] : '',
                 'end_longitude' => isset($parsedBody['destination']['1']) ? $parsedBody['destination']['1'] : '',
            ];

            $isValid = $apiHelper->validateLatitudeLongitude($params);
            $statusCode = 200;
        } else {
            $information = ['error' => 'ENTERED_DATA_IS_NOT_VALID'];
            if ($validationResponse) {
                $information = ['error' => $validationResponse['error']];
            }
            $statusCode = 404;
        }

        if ($isValid) {
            $order = new Order();
            $result = $order->createOrder($params);
            if (0 === $result) {
                $information = ['message' => 'ORDER_NOT_CREATED'];
                $statusCode = 500;
            } else {
                $information = [
                    'id' => $result['id'],
                    'distance' => $result['distance'],
                    'status' => $result['status'],
                ];
            }
        } else {
            $information = ['error' => 'ENTERED_DATA_IS_NOT_VALID'];
            $statusCode = 404;
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

        $information = ['error' => 'ENTERED_BODY_OR_ORDERID_NOT_CORRECT'];
        $statusCode = 404;

        if ('TAKEN' === $parsedBody['status'] && is_numeric($orderID)) {
            $order = new Order();
            $result = $order->updateOrder($orderID);
            $statusCode = 200;

            $information = [];

            switch ($result) {
                case 0:
                    $information = ['error' => 'ORDER_ALREADY_BEEN_TAKEN'];
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
