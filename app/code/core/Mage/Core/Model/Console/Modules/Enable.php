<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mage_Core_Model_Console_Modules_Enable extends Mage_Core_Model_Console_Modules_Abstract
{
    protected function configure()
    {
        $this->setName('modules:enable')
            ->setDescription('Enable module')
            ->addArgument('module', InputArgument::REQUIRED, 'Module to enable');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module = $input->getArgument('module');
        $this->toggleModuleActive($module, true);

        return Command::SUCCESS;
    }

    /**
     * @return array
     */
    protected function getModules(): array
    {
        return $this->getConfigHelper()->listModules(null, false);
    }
}
