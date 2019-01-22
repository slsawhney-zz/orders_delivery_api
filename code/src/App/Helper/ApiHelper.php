<?php

namespace App\Helper;

class ApiHelper
{
    /**
     * @param int $limit
     * @param int $page
     *
     * @return array
     */
    public function pageLimitCheck($limit, $page)
    {
        $information = [];
        if (!isset($limit) || !isset($page)) {
            $information = ['error' => 'REQUEST_PARAMETER_MISSING'];
        }
        if (!is_numeric($limit) || !is_numeric($page)) {
            $information = ['error' => 'INVALID_PARAMETER_TYPE'];
        }
        if ($limit < 1 || $page < 1) {
            $information = ['error' => 'INVALID_PARAMETERS'];
        }

        return $information;
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
    public function verifyRequiredParams($required_fields, $parsedBody)
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
     * Validating Latitude and Longnitude inputs counts.
     *
     * @param array $parsedBody
     *
     * @return bool
     */
    public function validateLatitudeLongitudeCount($parsedBody)
    {
        $flag = false;
        if (2 == count($parsedBody['origin']) && 2 == count($parsedBody['destination'])) {
            $flag = true;
        }

        return $flag;
    }

    /**
     * Validating Latitude and Longnitude inputs.
     *
     * @param array $params
     *
     * @return bool
     */
    public function validateLatitudeLongitude($params)
    {
        $isValidLatitudeLongitude = $isRangeCorrect = $isTypeCorrect = $isStartEndCorrect = $isStartAndEndCorrect = $isOtherInfoCorrect = false;
        $isOtherInfoCorrect = $this->validateLatitudeLongitudeOtherInfo($params); // Check Latitude Longitude Empty condition.
        $isRangeCorrect = $this->checkLatitudeLongitudeRange($params); // Check Latitude Longitude Range condition.
        $isTypeCorrect = $this->checkLatitudeLongitudeType($params); // Check Latitude Longitude Data Type.
        $isStartAndEndCorrect = $this->checkLatitudeLongitudeStartAndEnd($params); // Check Latitude Longitude Start and End value.

        if ($isOtherInfoCorrect && $isRangeCorrect && $isTypeCorrect && $isStartAndEndCorrect) {
            $isValidLatitudeLongitude = true;
        }

        return $isValidLatitudeLongitude;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function validateLatitudeLongitudeOtherInfo($params)
    {
        $isOtherInfoCorrect = true;
        if (
            '' == $params['start_latitude'] ||
            '' == $params['start_longitude'] ||
            '' == $params['end_latitude'] ||
            '' == $params['end_longitude']
        ) {
            $isOtherInfoCorrect = false;
        } elseif ((trim($params['start_latitude'], '0') != (float) $params['start_latitude']) &&
            (trim($params['start_longitude'], '0') != (float) $params['start_longitude'])
        ) {
            $isOtherInfoCorrect = false;
        } elseif ((trim($params['end_latitude'], '0') != (float) $params['end_latitude']) &&
            (trim($params['end_longitude'], '0') != (float) $params['end_longitude'])
        ) {
            $isOtherInfoCorrect = false;
        }

        return $isOtherInfoCorrect;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function checkLatitudeLongitudeRange($params)
    {
        $isRangeCorrect = true;
        if ($params['start_latitude'] <= -90 || $params['start_latitude'] >= 90) {
            $isRangeCorrect = false;
        } elseif ($params['end_latitude'] <= -90 || $params['end_latitude'] >= 90) {
            $isRangeCorrect = false;
        } elseif ($params['start_longitude'] <= -180 || $params['start_longitude'] >= 180) {
            $isRangeCorrect = false;
        } elseif ($params['end_longitude'] <= -180 || $params['end_longitude'] >= 180) {
            $isRangeCorrect = false;
        }

        return $isRangeCorrect;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function checkLatitudeLongitudeType($params)
    {
        $isTypeCorrect = false;

        if (is_float($params['start_latitude']) || is_float($params['end_latitude']) || is_float($params['start_longitude']) || is_float($params['end_longitude'])) {
            $isTypeCorrect = true;
        }

        return $isTypeCorrect;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function checkLatitudeLongitudeStartAndEnd($params)
    {
        $isStartAndEndCorrect = true;
        if ($params['start_latitude'] == $params['end_latitude'] ||
            $params['start_longitude'] == $params['end_longitude'] ||
            $params['start_latitude'] == $params['start_longitude'] ||
            $params['end_latitude'] == $params['end_longitude']) {
            $isStartAndEndCorrect = false;
        }

        return $isStartAndEndCorrect;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getDistance($params)
    {
        $origin = $params['start_latitude'].','.$params['start_longitude'];
        $destination = $params['end_latitude'].','.$params['end_longitude'];

        $mapApi = getenv('GOOGLE_API_URL').'&origins='.$origin.'&destinations='.$destination.'&key='.getenv('GOOGLE_API_KEY'); // Google Map API URL.

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $mapApi); // Request to get distance info.
        $data = json_decode($res->getBody());

        if ('NOT_FOUND' === $data->rows[0]->elements[0]->status || !$data) {
            return false;
        }

        return (int) $data->rows[0]->elements[0]->distance->value;
    }
}
