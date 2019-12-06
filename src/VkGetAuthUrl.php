<?php

declare(strict_types=1);

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class VkGetAuthUrl extends Command
{
    protected static $defaultName = 'vk:auth:url';
    protected array $config;

    public function __construct(string $name = null, array $config = [])
    {
        parent::__construct($name);
        $this->config = $config;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oauth = new VKOAuth();
        $redirect_uri = 'https://oauth.vk.com/blank.html';
        $client_id = $this->config['vk_client'];

        $token = $oauth->getAuthorizeUrl(VKOAuthResponseType::TOKEN, $client_id, $redirect_uri, VKOAuthDisplay::PAGE, [VKOAuthUserScope::OFFLINE]);

        $output->writeln(\sprintf('Token: %s', $token));

        return 0;
    }
}