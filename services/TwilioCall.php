<?php

class TwilioCall
{
    private $accountSid;
    private $authToken;
    private $twilioNumber;

    public function __construct($accountSid, $authToken, $twilioNumber)
    {
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->twilioNumber = $twilioNumber;
    }

    public function makeCall($toNumber, $repeatCount = 2, $delayBetweenCalls = 5)
    {
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Calls.json";
        $results = [];

        for ($i = 0; $i < $repeatCount; $i++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->accountSid}:{$this->authToken}");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'To' => $toNumber,
                'From' => $this->twilioNumber,
                'Url' => 'http://demo.twilio.com/docs/voice.xml'
            ]));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response) {
                $responseData = json_decode($response, true);
                if (isset($responseData['sid'])) {
                    $results[] = [
                        'callNumber' => $i + 1,
                        'status' => 'success',
                        'sid' => $responseData['sid']
                    ];
                } else {
                    $results[] = [
                        'callNumber' => $i + 1,
                        'status' => 'error',
                        'message' => $responseData['message'] ?? 'Unknown error'
                    ];
                }
            } else {
                $results[] = [
                    'callNumber' => $i + 1,
                    'status' => 'error',
                    'message' => 'Error en la conexión a la API de Twilio'
                ];
            }

            if ($i < $repeatCount - 1) {
                sleep($delayBetweenCalls); 
            }
        }

        return json_encode($results, JSON_PRETTY_PRINT);
    }
}