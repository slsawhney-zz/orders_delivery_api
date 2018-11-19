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
    public function pageLimitCheck($limit, $page){
         $information = [];
         if (!isset($limit) || !isset($page)) {
            $information = ['error' => 'REQUEST_PARAMETER_MISSING'];
        }
        if (!is_numeric($limit) || !is_numeric($page)) {
            $information = ['error' => 'INVALID_PARAMETER_TYPE'];
        }
        if ($limit  < 1 || $page  < 1) {
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
    public function validateLatitudeLongitudeCount($parsedBody){

        $flag = false;
        if((count($parsedBody['origin']) == 2) && (count($parsedBody['destination']) == 2)){
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
        $flag = false;
        $flag = $this->checkLatitudeLongitudeRange($params);
        $flag = $this->checkLatitudeLongitudeStartAndEnd($params);
        $flag = $this->checkLatitudeLongitudeStartAndEnd($params);

        if (
            $params['start_latitude'] == '' ||
            $params['start_longitude'] == '' ||
            $params['end_latitude'] == '' ||
            $params['end_longitude'] == ''
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

    /**
     * @param array $params
     *
     * @return bool
     */
    public function checkLatitudeLongitudeRange($params)
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
        }

        return flag;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function checkLatitudeLongitudeType($params)
    {
        $flag = false;
        if (is_string($params['start_latitude']) || is_string($params['end_latitude']) || is_string($params['start_longitude']) || is_string($params['end_longitude'])) {
            $flag = false;
        }

        return flag;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function checkLatitudeLongitudeStartAndEnd($params)
    {
        $flag = false;
        if ($params['start_latitude'] === $params['end_latitude'] ||
            $params['start_longitude'] === $params['end_longitude'] ||
            $params['start_latitude'] === $params['start_longitude'] ||
            $params['end_latitude'] === $params['end_longitude']) {
            $flag = false;
        }

        return flag;
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
