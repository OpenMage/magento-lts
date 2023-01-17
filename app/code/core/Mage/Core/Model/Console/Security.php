<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mage_Core_Model_Console_Security extends Mage_Console_Model_Command
{
    protected function configure()
    {
        $this->setName('dev:secuirty')
            ->setDescription('Secuirty check');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (posix_geteuid() == 0) {
            $output->writeln('Don\'t run OpenMage as root!');
        }

        return Command::SUCCESS;
    }
}
