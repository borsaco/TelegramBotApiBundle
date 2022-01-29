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

class WebhookSetCommand extends Command
{
    /**
     * @var Bot
     */
    private $bot;

    protected static $defaultName = 'telegram:bot:webhook:set';

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
            ->addArgument('url', InputArgument::OPTIONAL, 'Webhook url')
            ->addArgument('certificate', InputArgument::OPTIONAL, 'Path to public key certificate')
            ->setDescription('Set Webhook')
            ->setHelp('This command allows you to set webhook for your bots')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
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

        if (null === $input->getArgument('url')) {
            $value = $io->askQuestion(new Question('Entet the webhook url', ''));
            $input->setArgument('url', $value);
        }

        if (null === $input->getArgument('certificate')) {
            $value = $io->askQuestion(new Question('Entet the certificate path'));
            $input->setArgument('certificate', $value);
        }

        try {
            $this->bot->getBot($input->getArgument('name'))->setWebhook(['url' => $input->getArgument('url'), 'certificate' => $input->getArgument('certificate')]);
        } catch (TelegramSDKException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf('Webhook url "%s" has been set for "%s" bot', $input->getArgument('url'), $input->getArgument('name')));

        return Command::SUCCESS;
    }
}