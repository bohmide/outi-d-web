<?php
namespace App\Service;

use Twilio\Rest\Client;

class SmsGenerator
{
   private string $twilioSid;
   private string $twilioToken;
   private string $twilioFromNumber; 
   private const TWILIO_SID = 'AC963b941fdd30e1e45a55c4c42a4393a2';
   private const TWILIO_TOKEN = 'a1c6db633f4a28cfbd6ac5a476b20502';
   private const TWILIO_NUMBER = '+15856696575';



    public function __construct()
    {
        $this->twilioSid = self::TWILIO_SID;
        $this->twilioToken = self::TWILIO_TOKEN;
        $this->twilioFromNumber = self::TWILIO_NUMBER;
    }

    public function sendSms(string $to, string $name, string $text): void
    {
        try {
            $client = new Client($this->twilioSid, $this->twilioToken);

            $message = $client->messages->create(
                $to, 
                [
                    'from' => $this->twilioFromNumber,
                    'body' => "Salut $name, $text"
                ]
            );

            if (!$message->sid) {
                throw new \Exception("L'envoi du SMS a échoué.");
            }
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de l'envoi du SMS : " . $e->getMessage());
        }
    }
}
?>
