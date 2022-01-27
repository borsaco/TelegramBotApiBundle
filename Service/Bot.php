<?php

namespace Borsaco\TelegramBotApiBundle\Service;

use Borsaco\TelegramBotApiBundle\DependencyInjection\Factory\BotFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Telegram\Bot\Api;

class Bot
{
    private $config;

    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->getParameter('telegram_bot_api.config');
    }

    public function getBot(?string $name = null): Api
    {
        $factory = new BotFactory();

        if ($name == null) {
            if ($this->config['default']) {
                return $factory->create($this->config, $this->config['default']);
            } else {
                return $factory->create($this->config, reset($this->config['bots']));
            }
        }

        return $factory->create($this->config, $name);
    }

    public function getNames(): array
    {
        return array_keys($this->config['bots']);
    }

    public function hasBot(string $name): bool
    {
        return isset($this->config['bots'][$name]);
    }

    public function isMaintenance(string $name): bool
    {
        return $this->hasBot($name) ? $this->config['bots'][$name]['maintenance'] : false;
    }
}
