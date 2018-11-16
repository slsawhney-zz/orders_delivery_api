<?php

namespace App\Controller;

//use Psr\Log\LoggerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Models\Order;

class ApiController
{
    private $logger;

    public function __invoke(RequestInterface $request, ResponseInterface $response, $args = [])
    {
        return $response->withJson($request->getQueryParams());
    }

    /*
        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }
    */

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

        $validationResponse = $this->verifyRequiredParams(array('origin', 'destination'), $parsedBody);

        $information = $params = [];
        $params = [
             'start_latitude' => $parsedBody['origin']['0'],
             'start_longitude' => $parsedBody['origin']['1'],
             'end_latitude' => $parsedBody['destination']['0'],
             'end_longitude' => $parsedBody['destination']['1'],
        ];

        $flag = $this->validateLatitudeLongnitude($params);
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

        $request_params = $this->verifyRequiredParams(array('status'), $postMethod, $parsedBody);

        $information = ['error' => 'Entered Body or OrderId is not valid'];
        $statusCode = 404;

        if ($parsedBody['status'] === 'TAKEN' && is_int($orderID)) {
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

    /**
     * Verifying Method and Request.
     *
     * @param array  $required_fields
     * @param string $postMethod
     * @param array  $parsedBody
     *
     * @return array
     */
    private function verifyRequiredParams($required_fields, $parsedBody)
    {
        $error = false;
        $error_fields = '';
        $response = [];

        foreach ($required_fields as $field) {
            if (empty($parsedBody[$field])) {
                $error = true;
                $error_fields .= $field.', ';
            }
        }

        if ($error) {
            $response = ['error' => 'Required field(s) '.substr($error_fields, 0, -2).' is missing or empty'];
        }

        return $response;
    }

    /**
     * Validating Latitude and Longnitude inputs.
     *
     * @param array $params
     *
     * @return bool
     */
    private function validateLatitudeLongnitude($params)
    {
        $flag = false;
        if ($params['start_latitude'] > -90.0 && $params['start_latitude'] < 90.0) {
            $flag = true;
        } elseif ($params['end_latitude'] > -90.0 && $params['end_latitude'] < 90.0) {
            $flag = true;
        } elseif ($params['start_longitude'] > -180.0 && $params['start_longitude'] < 180.0) {
            $flag = true;
        } elseif ($params['end_longitude'] > -180.0 && $params['end_longitude'] < 180.0) {
            $flag = true;
        } elseif (is_string($params['start_latitude'])) {
            $flag = false;
        } elseif (is_string($params['end_latitude'])) {
            $flag = false;
        } elseif (is_string($params['start_longitude'])) {
            $flag = false;
        } elseif (is_string($params['end_longitude'])) {
            $flag = false;
        } elseif (
            $params['start_latitude'] === $params['end_latitude'] ||
            $params['start_longitude'] === $params['end_longitude'] ||
            $params['start_latitude'] === $params['start_longitude'] ||
            $params['end_latitude'] === $params['end_longitude']
        ) {
            $flag = false;
        } elseif (
            $params['start_latitude'] === '' ||
            $params['start_longitude'] === '' ||
            $params['start_latitude'] === '' ||
            $params['end_latitude'] === ''
        ) {
            $flag = false;
        } elseif ((trim($params['start_latitude'], '0') != (float) $params['start_latitude']) &&
            (trim($params['start_longitude'], '0') != (float) $params['start_longitude'])
        ) {
            $flag = false;
        } elseif ((trim($params['end_latitude'], '0') != (float) $params['end_latitude']) &&
            (trim($params['end_longitude'], '0') != (float) $params['end_longitude'])
        ) {
            $flag = false;
        }

        return $flag;
    }
}
