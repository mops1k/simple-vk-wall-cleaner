<?php

declare(strict_types=1);

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use VK\Client\VKApiClient;

class VkWallClear extends Command
{
    protected static $defaultName = 'vk:wall:clear';
    protected array $config;

    public function __construct(string $name = null, array $config = [])
    {
        parent::__construct($name);
        $this->config = $config;
    }

    protected function configure()
    {
        $this->setDescription('Delete all posts on vkontakte wall');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $client = new VKApiClient();
        $removedCount = 0;
        $progressStarted = false;
        do {
            $posts = $client->wall()->get($this->config['vk_token'], [
                'owner_id' => $this->config['vk_owner'],
                'count' => 100
            ]);

            if (!$progressStarted) {
                $io->progressStart((int) ($posts['response']['count'] ?? 0));
            }

            foreach (($posts['response']['items'] ?? []) as $item) {
                $client->wall()->delete($this->config['vk_token'], [
                    'owner_id' => $this->config['vk_owner'],
                    'post_id'  => $item['id']
                ]);

                ++$removedCount;
                sleep(0.3);
                $io->progressAdvance();
            }
        } while (($posts['response']['count'] ?? 0) > 0);
        $io->progressFinish();

        $io->success(\sprintf('Total removed posts on wal is %d', $removedCount));
        return 0;
    }
}
