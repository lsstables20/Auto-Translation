<?php

namespace Twenty20\Translation\Services;

use GuzzleHttp\Client;
use Exception;

class TranslationService
{
    protected $client;
    protected $apiKey;
    protected $service;

    public function __construct($service, $apiKey)
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
        $this->service = $service;
    }

    public function translate($text, $targetLanguage, $sourceLanguage = 'en')
    {
        switch ($this->service) {
            case 'google':
                return $this->translateWithGoogle($text, $targetLanguage, $sourceLanguage);
            case 'deepl':
                return $this->translateWithDeepL($text, $targetLanguage, $sourceLanguage);
            case 'openai':
                return $this->translateWithOpenAI($text, $targetLanguage, $sourceLanguage);
            default:
                throw new Exception('Unsupported translation service.');
        }
    }

    protected function translateWithGoogle($text, $targetLanguage, $sourceLanguage)
    {
        $response = $this->client->post('https://translation.googleapis.com/language/translate/v2', [
            'query' => [
                'key' => $this->apiKey,
            ],
            'json' => [
                'q' => $text,
                'target' => $targetLanguage,
                'source' => $sourceLanguage,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['data']['translations'][0]['translatedText'];
    }

    protected function translateWithDeepL($text, $targetLanguage, $sourceLanguage)
    {
        $response = $this->client->post('https://api.deepl.com/v2/translate', [
            'headers' => [
                'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
            ],
            'json' => [
                'text' => [$text],
                'target_lang' => $targetLanguage,
                'source_lang' => $sourceLanguage,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['translations'][0]['text'];
    }

    protected function translateWithOpenAI($text, $targetLanguage, $sourceLanguage)
    {
        $response = $this->client->post('https://api.openai.com/v1/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'json' => [
                'model' => 'text-davinci-003',
                'prompt' => "Translate the following text from $sourceLanguage to $targetLanguage: $text",
                'max_tokens' => 100,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['choices'][0]['text'];
    }
}
