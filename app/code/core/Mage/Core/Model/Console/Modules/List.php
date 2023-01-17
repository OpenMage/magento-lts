<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mage_Core_Model_Console_Modules_List extends Mage_Console_Model_Command
{
    protected function configure()
    {
        $this->setName('modules:list')
            ->setDescription('Command Description')
            ->setHelp("Help Help!");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table
            ->setHeaders(['codePool', 'Name', 'Version', 'Status', 'Used by'])
            ->setRows(Mage::helper('core/config')->listModules(null,true, true))
            ->render();

        return Command::SUCCESS;
    }
}
