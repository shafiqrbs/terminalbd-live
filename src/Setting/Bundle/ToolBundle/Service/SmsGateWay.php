<?php

namespace Setting\Bundle\ToolBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class SmsGateWay
{

    private $username;
    private $password;

    /**
     * @var Client
     */
    private $client;

    public function  __construct($username, $password, Client $client)
    {

        $this->username = $username;
        $this->password = $password;
        $this->password = $password;
        $this->client = $client;
    }

    function sendPrevious($msg, $phone){

        try {

            $body = '{"authentication": {"username": "' . $this->username .'","password": "'.$this->password.'"},"messages": [{"sender": "8804445651233","text": "'.$msg.'","recipients": [{"gsm": "'.$phone.'"}]}]}';

            $response = $this->client->post(
                "/api/v3/sendsms/json",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept'       => '/',
                    ],
                    'body'    => $body,
                ]
            );
            $content  = $response->getBody()->getContents();
            return 'success';

        } catch (RequestException $e) {
            //var_dump($e->getRequest());
            if ($e->hasResponse()) {
                // var_dump($e->getResponse()->getReasonPhrase());
            }
            return 'failed';
        }

    }


    function sendx($msg, $phone, $sender = ""){

        if(empty($sender)){
            $from = "03590602016";
        }else{
            $from = $sender;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.icombd.com/api/v1/campaigns/sms/1/text/single",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\"from\":\"{$from}\",\"text\":\"{$msg}\",\"to\":\"{$phone}\"}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic dW1hcml0OnVtYXJpdDE0OA=="
            ),
        ));
        $response = curl_exec($curl);
        print_r(curl_error($curl));
        curl_close($curl);
        return 'success';

    }

    function send($msg, $phone, $sender = ""){
        $curl = curl_init();
        $data =array(
            'apikey' => '198a7497a859e5fe',
            'secretkey' => 'cb6adaba',
            'callerID' => '8809612770474',
            'toUser' => $phone,
            'messageContent' => $msg,
        );
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://smpp.ajuratech.com:7790/sendtext",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        print_r(curl_error($curl));
        curl_close($curl);
        return 'success';

    }
}