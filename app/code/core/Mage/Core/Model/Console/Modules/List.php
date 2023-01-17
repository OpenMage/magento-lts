<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mage_Core_Model_Console_Modules_List extends Mage_Core_Model_Console_Modules_Abstract
{
    protected function configure()
    {
        $this->setName('modules:list')
            ->setDescription('List modules');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table
            ->setHeaders(['codePool', 'Name', 'Version', 'Status', 'Used by'])
            ->setRows($this->getModules())
            ->render();

        return Command::SUCCESS;
    }

    /**
     * @return array
     */
    protected function getModules(): array
    {
        return $this->getConfigHelper()->listModules(null,null, null);
    }
}
