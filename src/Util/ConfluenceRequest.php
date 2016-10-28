<?php

/**
 * Created by PhpStorm.
 * User: ytan
 * Date: 10/28/16
 * Time: 12:09 AM
 */
class ConfluenceRequest
{
    private $curl;
    private $headers;

    public function __construct($url,$action,$data=null){
        $this->curl = curl_init();
        curl_setopt_array($this->curl,[
                CURLOPT_URL=>$url,
                CURLOPT_HEADER=>['Content-Type:application/json'],
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => $action]
        );
    }

    public function setAuthentication($username,$password){
        curl_setopt_array($this->curl,[
                CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
                CURLOPT_USERPWD=> $username . ':' . $password
            ]
        );
    }

    public function execRequest(){
        $serverOutput = curl_exec ($this->curl);
        curl_close ($this->curl);
        if (!$serverOutput) {
            throw new \Exception('Error: "' . curl_error($this->curl) . '" - Code: ' . curl_errno($this->curl));
        }
        else {
            return json_encode($serverOutput);
        }
    }
}