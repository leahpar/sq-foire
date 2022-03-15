<?php

namespace App\Service;


use Ovh\Api;

class SMSService
{

    private string $OVH_API_APP_KEY;
    private string $OVH_API_APP_SECRET;
    private string $OVH_API_ENDPOINT;
    private string $OVH_API_CONSUMER_KEY;
    private string $OVH_SMS_SERVICE;

    public function __construct(
        string $OVH_API_APP_KEY,
        string $OVH_API_APP_SECRET,
        string $OVH_API_ENDPOINT,
        string $OVH_API_CONSUMER_KEY,
        string $OVH_SMS_SERVICE
    ) {
        $this->OVH_API_APP_KEY = $OVH_API_APP_KEY;
        $this->OVH_API_APP_SECRET = $OVH_API_APP_SECRET;
        $this->OVH_API_ENDPOINT = $OVH_API_ENDPOINT;
        $this->OVH_API_CONSUMER_KEY = $OVH_API_CONSUMER_KEY;
        $this->OVH_SMS_SERVICE = $OVH_SMS_SERVICE;
    }

    public function send(String $target, String $message)
    {
        try {
            $conn = new Api(
                $this->OVH_API_APP_KEY,
                $this->OVH_API_APP_SECRET,
                $this->OVH_API_ENDPOINT,
                $this->OVH_API_CONSUMER_KEY
            );

            //dump($target);
            $target = preg_replace('#[^0-9\+]#', '', $target);
            //dump($target);
            $target = preg_replace('#^0#', '+33', $target);
            //dump($target);

            //$smsServices = $conn->get('/sms/');
            //foreach ($smsServices as $smsService) {
            //    dump($smsService);
            //}

            //$smsService = getenv("OVH_SMS_SERVICE");
            $smsService = $this->OVH_SMS_SERVICE;

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

            dump($resultPostJob);
            //return $resultPostJob
        }
        catch (\Exception $e) {
            dump($e);
        }
    }
}
