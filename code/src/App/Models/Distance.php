<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    protected $table = 'distance';

    public function isDistanceExists($params)
    {
        $matchCase = [
          'start_latitude' => $params['start_latitude'],
          'start_longitude' => $params['start_longitude'],
          'end_latitude' => $params['end_latitude'],
          'end_longitude' => $params['end_longitude'],
       ];

        return self::Where($matchCase)->take(1)->get()->toArray();
    }

    public function saveDistance($params, $distance)
    {
        $id = self::insertGetId([
          'start_latitude' => $params['start_latitude'],
          'start_longitude' => $params['start_longitude'],
          'end_latitude' => $params['end_latitude'],
          'end_longitude' => $params['end_longitude'],
          'distance' => $distance,
        ]);

        return [0 => ['id' => $id, 'distance' => $distance]];
    }
}

