<?php

namespace Borsaco\TelegramBotApiBundle\Command;

use Borsaco\TelegramBotApiBundle\Service\Bot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Telegram\Bot\Exceptions\TelegramSDKException;
use TelegramBot\Api\BotApi;

class WebhookInfoCommand extends Command
{
    /**
     * @var Bot
     */
    private $bot;

    protected static $defaultName = 'telegram:bot:webhook:info';

    /**
     * @inheritDoc
     */
    public function __construct(Bot $bot)
    {
        parent::__construct(null);

        $this->bot = $bot;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'The bot name (is set in configuration file)')
            ->setDescription('Webhook Information')
            ->setHelp('This command fetch information about the bot webhook')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if (null === $input->getArgument('name')) {
            $value = $io->askQuestion(new ChoiceQuestion('Entet the name of the bot', $this->bot->getNames()));
            $input->setArgument('name', $value);
        }

        if (!$this->bot->hasBot($input->getArgument('name'))) {
            $io->error('There is no bot with this name.');
            return Command::FAILURE;
        }

        try {
            $information = $this->bot->getBot($input->getArgument('name'))->getWebhookInfo();
        } catch (TelegramSDKException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->table(
            ['URL', 'Has Certificate', 'Pending Updates'],
            [
                [$information->get('url') === "" ? 'Not Set' : $information->get('url')  , $information->get('has_custom_certificate') ? 'Yes' : 'No', $information->get('pending_update_count')]
            ]
        );

        return Command::SUCCESS;
    }
}