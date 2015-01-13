<?php

namespace jannieforms\sanitizers;

use jannieforms\sanitizers;

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
        if (preg_match('/^[0-9]{1,10}$/', $value) && $this->openIbanUsername !== null && $this->openIbanPassword !== null)
            $value = self::convertUsingOpenIban($value, $this->openIbanUsername, $this->openIbanPassword);
        $parts = str_split($value, 4);
        return implode(' ', $parts);
    }

    /**
     * Converst BBAN number into IBAN number using the openiban API
     *
     * @param $bban
     * @param $username
     * @param $password
     *
     * @return string|null
     */
    private static function convertUsingOpenIban ($bban, $username, $password)
    {
        $host    = self::ENDPOINT . $bban;
        $apiCall = curl_init(self::ENDPOINT . $bban);
        curl_setopt($apiCall, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($apiCall, CURLOPT_RETURNTRANSFER, true);
        $response    = curl_exec($apiCall);
        $apiResponse = json_decode($response, true);
        curl_close($apiCall);
        if ($apiResponse === null)
            return null;
        else
            return $apiResponse['iban'];
    }

}