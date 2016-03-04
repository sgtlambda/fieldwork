<?php

namespace fieldwork\sanitizers;

use fieldwork\sanitizers;
//use GuzzleHttp\Client;

class IbanSanitizer extends sanitizers\FieldSanitizer
{

    const ENDPOINT = 'http://opendata.siteworkers.nl/openiban?bban=';

    private $openIbanUsername,
        $openIbanPassword;

    function __construct ($openIbanUsername = null, $openIbanPassword = null)
    {
        $this->openIbanUsername = $openIbanUsername;
        $this->openIbanPassword = $openIbanPassword;
    }

    /**
     * @param $bban
     * @return string
     */
    private static function getRequestEndpoint ($bban)
    {
        return self::ENDPOINT . $bban;
    }

    public function describeObject ()
    {
        return 'iban';
    }

    public function isLive ()
    {
        return true;
    }

    public function isRealtime ()
    {
        return false;
    }

    public function sanitize ($value)
    {
        $value = preg_replace('/\s/', '', $value);
        if (preg_match('/^[0-9]{1,10}$/', $value) && $this->openIbanUsername !== null && $this->openIbanPassword !== null) {
            $convertedValue = self::convertUsingOpenIban($value, $this->openIbanUsername, $this->openIbanPassword);
            if ($convertedValue !== null)
                $value = $convertedValue;
        }
        $parts = str_split($value, 4);
        return implode(' ', $parts);
    }

    /**
     * Converst BBAN number into IBAN number using the openiban API
     *
     * @param string $bban
     * @param string $username
     * @param string $password
     *
     * @return string|null
     */
    private static function convertUsingOpenIban ($bban, $username, $password)
    {
//        try {
//            $client      = new Client();
//            $res         = $client->request('GET', self::getRequestEndpoint($bban), [
//                'auth' => [$username, $password]
//            ]);
//            $apiResponse = json_decode($res->getBody());
//            return $apiResponse === null ? null : $apiResponse['iban'];
//        } catch (\Exception $e) {
            return null;
//        }
    }

}