<?php

namespace App\Service;


use Ovh\Api;

class SMSService
{

    public function __construct(private readonly string $OVH_API_APP_KEY, private readonly string $OVH_API_APP_SECRET, private readonly string $OVH_API_ENDPOINT, private readonly string $OVH_API_CONSUMER_KEY, private string $OVH_SMS_SERVICE)
    {
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

            $target = preg_replace('#[^0-9\+]#', '', $target);
            $target = preg_replace('#^0#', '+33', $target);

            //$smsServices = $conn->get('/sms/');
            //foreach ($smsServices as $smsService) {
            //    dump($smsService);
            //}

            $smsService = $this->OVH_SMS_SERVICE;

            $content = (object)[
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
            ];
            $resultPostJob = $conn->post('/sms/' . $smsService . '/jobs/', $content);

            //dump($resultPostJob);
            //return $resultPostJob
        }
        catch (\Exception) {
            //dump($e);
        }
    }
}
