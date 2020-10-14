<?php

declare(strict_types=1);

namespace App\Console;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;
use function sprintf;

class ClearCacheCommand extends Command
{
    public const DEFAULT_NAME = 'primo:clear-cache';

    /** @var CacheItemPoolInterface[] */
    private $cachePools;

    public function __construct(CacheItemPoolInterface ...$cachePools)
    {
        $this->cachePools = $cachePools;
        parent::__construct(self::DEFAULT_NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Clears cache pools associated with the website and content');
        $this->setHelp('This command has no arguments or options');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->clear();
        $style = new SymfonyStyle($input, $output);
        $style->success(sprintf(
            '%s cache pools have been cleared',
            count($this->cachePools)
        ));

        return 0;
    }

    private function clear(): void
    {
        foreach ($this->cachePools as $pool) {
            $pool->clear();
        }
    }
}
