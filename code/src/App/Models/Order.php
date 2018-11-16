<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';

    public $timestamps = false;

    /**
     * @param int $startFrom
     * @param int $limit
     *
     * @return array
     */
    public function getOrders($startFrom, $limit)
    {
        $orders = self::select('order.*')
                    ->join('distance', 'distance.id', '=', 'order.distance_id')
                    ->select('order.id', 'distance.distance', 'order.status')
                    ->skip($startFrom)
                    ->take($limit)
                    ->get();

        return $this->parseOrdersStatus($orders);
    }

    /**
     * @param OrderObject $orders
     *
     * @return array
     */
    public function parseOrdersStatus($orders)
    {
        $orderData = [];
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $orderData[] = [
                    'id' => $order->id,
                    'distance' => $order->distance,
                    'status' => ($order->status == 1) ? 'TAKEN' : 'UNASSIGNED',
               ];
            }
        }

        return $orderData;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function createOrder($params)
    {
        $distance = '';
        $distanceObject = new Distance();
        $distanceArray = $distanceObject->isDistanceExists($params);

        if (empty($distanceArray)) {
            $distance = $this->getDistance($params);
            if (!$distance) {
                return 0;
            }
            $distanceArray = $distanceObject->saveDistance($params, $distance);
        }

        $orderID = self::insertGetId([
            'status' => 0,
            'distance_id' => $distanceArray[0]['distance'],
        ]);

        return $returnParams = [
            'id' => $orderID,
            'distance' => $distanceArray[0]['distance'],
            'status' => 'UNASSIGN',
        ];
    }

    /**
     * Method to update order status.
     *
     * @param int $id
     */
    public function updateOrder($id)
    {
        $orderStatus = $this->getOrderStatus($id);

        if (!is_null($orderStatus) && $orderStatus === 0) {
            self::where('id', $id)->update(['status' => 1]);

            return 1;
        } elseif ($orderStatus === 1) {
            return 0;
        } else {
            return 2;
        }
    }

    /**
     * Method to get order status.
     *
     * @param int $id
     */
    private function getOrderStatus($id)
    {
        $matchCase = ['id' => $id];

        return self::Where($matchCase)->pluck('status')->sortByDesc('id')->first();
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function getDistance($params)
    {
        $origin = $params['start_latitude'].','.$params['start_longitude'];
        $destination = $params['end_latitude'].','.$params['end_longitude'];

        $mapApi = getenv('GOOGLE_API_URL').'&origins='.$origin.'&destinations='.$destination.'&key='.getenv('GOOGLE_API_KEY');

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $mapApi);
        $data = json_decode($res->getBody());

        if ($data->rows[0]->elements[0]->status === 'NOT_FOUND' || !$data) {
            return false;
        }

        return (int) $data->rows[0]->elements[0]->distance->value;
    }
}
