<?php
namespace App\Service;


use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;


class TextToSpeechService
{
    private $client;

    public function __construct()
    {
        $this->client = new TextToSpeechClient();
    }

    public function synthesizeSpeech(string $text): string
    {
        // Create the SynthesisInput
        $synthesisInputText = (new SynthesisInput())
            ->setText($text);

        // Create the VoiceSelectionParams
        $voice = (new VoiceSelectionParams())
            ->setLanguageCode('en-US')
            ->setSsmlGender(SsmlVoiceGender::FEMALE);

        // Create the AudioConfig
        $audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3);

        // Create the SynthesizeSpeechRequest
        $synthesizeSpeechRequest = (new SynthesizeSpeechRequest())
            ->setInput($synthesisInputText)
            ->setVoice($voice)
            ->setAudioConfig($audioConfig);

        // Call the API to synthesize speech
        $response = $this->client->synthesizeSpeech($synthesizeSpeechRequest);

        // Return the audio content as a string
        return $response->getAudioContent();
    }
}