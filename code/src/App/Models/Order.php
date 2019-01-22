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
                    ->leftJoin('distance', 'distance.id', '=', 'order.distance_id')
                    ->select('order.id', 'distance.distance', 'order.status')
                    ->skip($startFrom)
                    ->take($limit)
                    ->get();

        return $this->parseOrdersStatus($orders); // Order Status in human readable format
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
                    'status' => (1 == $order->status) ? 'TAKEN' : 'UNASSIGNED',
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
        $distanceArray = $distanceObject->isDistanceExists($params); // Check existing distance for given set.

        if (empty($distanceArray)) {
            $appHelper = new \App\Helper\ApiHelper();
            $distance = $appHelper->getDistance($params);
            if (!$distance) {
                return 0;
            }
            $distanceArray = $distanceObject->saveDistance($params, $distance);
        }

        $orderID = self::insertGetId([
            'status' => 0,
            'distance_id' => $distanceArray[0]['id'],
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
        $orderStatus = $this->getOrderStatus($id); // Check order status.

        if (!is_null($orderStatus) && 0 === $orderStatus) {
            self::where('id', $id)->update(['status' => 1]);

            return 1;
        } elseif (1 === $orderStatus) {
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
        $matchCase = ['id' => $id, 'status' => 0]; //Check Race Condition.

        return self::Where($matchCase)->pluck('status')->sortByDesc('id')->first();
    }
}
