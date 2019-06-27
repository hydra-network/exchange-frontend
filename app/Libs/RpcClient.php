<?php

namespace App\Libs;

class RpcClient
{
    private $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    
    public function generateAddress()
    {
        return $this->sendQuery('/?command=generateAddress');
    }

    public function getIncome($address)
    {
        return $this->sendQuery('/?command=getIncome&address=' . $address);
    }
    
    public function getOutcomeTransaction($txId, $address)
    {
        return $this->sendQuery('/?command=getTransaction&txId=' . $txId . '&address=' . $address . '&type=send');
    }
    
    public function validateAddress($address)
    {
        return $this->sendQuery('/?command=validateAddress&address=' . $address);
    }

    public function isAddressValid($address)
    {
        $res = $this->validateAddress($address);
        
        return $res->isvalid;
    }
    
    public function send($address, $amount)
    {
        return $this->sendQuery('/?command=send&address=' . $address . '&amount=' . $amount);
    }
    
    protected function sendQuery($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '' . $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        //curl_setopt($ch, CURLOPT_USERPWD, "{$this->user}:{$this->password}");
        
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $result = curl_exec ($ch);

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close($ch);

        return json_decode($result);
    }
}
