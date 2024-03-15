<?php

namespace RetailCrm\ServiceBundle\Tests\Fixtures\App;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'test'
)]
class TestCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument(
                'argument',
                InputArgument::REQUIRED
            )
            ->addOption(
                'option',
                null,
                InputOption::VALUE_REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo self::getDefaultName() . ' ' . $input->getArgument('argument') . ' ' . $input->getOption('option');

        return Command::SUCCESS;
    }
}
