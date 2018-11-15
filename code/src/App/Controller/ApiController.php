<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Models\Distance;
use App\Models\Order;

class ApiController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function orders(RequestInterface $request, ResponseInterface $response, $args)
    {
        $page = $args['page'];
        $limit = $args['limit'];

        $page = $page ?? getenv('START');
        $limit = $limit ?? getenv('LIMIT');

        $startFrom = ($page == 1) ? 0 : (($page - 1) * getenv('LIMIT'));

        $order = new Order();
        $orders = $order->getOrders($startFrom, $limit);
        $this->echoResponse(200, $orders, $response);
    }

    public function saveOrder(RequestInterface $request, ResponseInterface $response, $args)
    {
        $postMethod = $request->getMethod();
        $parsedBody = $request->getParsedBody();

        $validationResponse = $this->verifyRequiredParams(array('origin', 'destination'), $postMethod, $parsedBody);

        $information = $params = [];
        $params = [
             'start_latitude' => $parsedBody['origin']['0'],
             'start_longitude' => $parsedBody['origin']['1'],
             'end_latitude' => $parsedBody['destination']['0'],
             'end_longitude' => $parsedBody['destination']['1'],
        ];

        $flag = $this->validateLatitudeLongnitude($params);

        if ($flag) {
            $order = new Order();
            $result = $order->createOrder($params);
            if ($res === 0) {
                $information['message'] = 'Order not created successfully';
                $this->echoResponse(500, $information, $response);
            } else {
                $information['id'] = $result['id'];
                $information['distance'] = $result['distance'];
                $information['status'] = $result['status'];
                $this->echoResponse(200, $information, $response);
            }
        } else {
            $information['error'] = 'Entered data is not valid';
            $this->echoResponse(400, $information, $response);
        }
    }

    public function echoResponse($statusCode, $information, $responseObj)
    {
        $responseObj = $responseObj->withStatus($statusCode);
        $responseObj = $responseObj->withJson($information);
        echo $responseObj;
    }

    public function updateOrder(RequestInterface $request, ResponseInterface $response, $args)
    {
        $postMethod = $request->getMethod();
        $parsedBody = $request->getParsedBody();
        $orderID = $args['id'];

        $request_params = $this->verifyRequiredParams(array('status'), $postMethod, $parsedBody);

        if ($parsedBody['status'] === 'taken') {
            $order = new Order();
            $result = $order->updateOrder($orderID);

            $information = [];
            if ($result === 2) {
                $information['error'] = 'INVALID_ORDER_ID';
                $this->echoResponse(404, $information, $response);
            } elseif ($result === 0) {
                $information['error'] = 'ORDER_ALREADY_BEEN_TAKEN';
                $this->echoResponse(409, $information, $response);
            } elseif ($result === 1) {
                $information['status'] = 'SUCCESS';
                $this->echoResponse(200, $information, $response);
            } else {
                $information['error'] = 'ERROR OCCURED';
                $this->echoResponse(500, $information, $response);
            }
        } else {
            $information['error'] = 'Entered data is not valid';
            $this->echoResponse(400, $information, $response);
        }
    }

    /**
     * Verifying Request Method and Request.
     *
     * @param array $required_fields
     */
    private function verifyRequiredParams($required_fields, $postMethod, $parsedBody)
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
            $response['error'] = 'Required field(s) '.substr($error_fields, 0, -2).' is missing or empty';
        }

        return $response;
    }

    /**
     * Validating inputs.
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

