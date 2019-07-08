<?php

namespace Borsaco\TelegramBotApiBundle\DependencyInjection\Factory;

use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

class BotFactory
{
    public function create(array $config = [], string $name)
    {
        $bot = new Api($config['bots'][$name]['token']);

        if($config['proxy']) {
            $client = new GuzzleHttpClient(new Client(['proxy' => $config['proxy']]));
            $bot->setHttpClientHandler($client);
        }

        if($config['async_requests']) {
            $bot->setAsyncRequest($config['async_requests']);
        }

        return $bot;
    }
}
