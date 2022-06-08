<?php

namespace Gerhardn\NatisBot\Helper;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class TelegramHelper
{
    private $parameterLoader;

    public function __construct()
    {
        $this->parameterLoader = new ParameterLoader();
    }

    public function sendMessage($message)
    {
        $client = new Client();

        $client->post(
            "https://api.telegram.org/bot{$this->getParameterLoader()->get('telegram_api_key')}/sendMessage",
            [RequestOptions::JSON => [
                'chat_id' => $this->getParameterLoader()->get('telegram_chat_id'),
                'text' => $message,
                'parse_mode' => 'MarkdownV2'
                ]
            ]
        );
    }

    protected function getParameterLoader() : ParameterLoader
    {
        return $this->parameterLoader;
    }
}