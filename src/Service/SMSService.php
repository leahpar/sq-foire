<?php

namespace App\Service;


use Ovh\Api;

class SMSService
{

    /**
     * @param $target
     * @param $message
     */
    public function send(String $target, String $message)
    {
        try {
            $conn = new Api(
                getenv("OVH_API_APP_KEY"),
                getenv("OVH_API_APP_SECRET"),
                getenv("OVH_API_ENDPOINT"),
                getenv("OVH_API_CONSUMER_KEY"));

            //dump($target);
            $target = preg_replace('#[^0-9\+]#', '', $target);
            //dump($target);
            $target = preg_replace('#^0#', '+33', $target);
            //dump($target);

            /*
            $smsServices = $conn->get('/sms/');
            foreach ($smsServices as $smsService) {

                print_r($smsService);
            }
            die();
            */

            $smsService = getenv("OVH_SMS_SERVICE");

            $content = (object)array(
                "charset" => "UTF-8",
                "class" => "phoneDisplay",
                "coding" => "7bit",
                "message" => $message,
                "noStopClause" => true,
                "priority" => "high",
                "receivers" => [$target],
                "senderForResponse" => true,
                //"sender" => ""  // Required if senderForResponse is false
                "validityPeriod" => 2880,
            );
            $resultPostJob = $conn->post('/sms/' . $smsService . '/jobs/', $content);

            //dump($resultPostJob);
            //return $resultPostJob
        }
        catch (\Exception $e) {
            // ...
        }
    }
}
