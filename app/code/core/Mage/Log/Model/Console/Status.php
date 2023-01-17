<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Mage_Log_Model_Console_Status extends Mage_Log_Model_Console_Abstract
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('log:status')
            ->setDescription('Display statistics per log tables');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $resource = $this->_getLog()->getResource();
        $adapter  = $resource->getReadConnection();
        // log tables
        $tables = [
            $resource->getTable('log/customer'),
            $resource->getTable('log/visitor'),
            $resource->getTable('log/visitor_info'),
            $resource->getTable('log/url_table'),
            $resource->getTable('log/url_info_table'),
            $resource->getTable('log/quote_table'),
            $resource->getTable('reports/viewed_product_index'),
            $resource->getTable('reports/compared_product_index'),
            $resource->getTable('reports/event'),
            $resource->getTable('catalog/compare_item'),
        ];

        $rows        = 0;
        $dataLengh   = 0;
        $indexLength = 0;

        $helper = Mage::helper('log/console');
        $tableRows = [];

        foreach ($tables as $table) {
            $query  = $adapter->quoteInto('SHOW TABLE STATUS LIKE ?', $table);
            $status = $adapter->fetchRow($query);
            if (!$status) {
                continue;
            }

            $rows += $status['Rows'];
            $dataLengh += $status['Data_length'];
            $indexLength += $status['Index_length'];

            $tableRows[] = [
                $table,
                $helper->humanCount($status['Rows']),
                $helper->humanCount($status['Data_length']),
                $helper->humanCount($status['Index_length']),
            ];
        }

        $tableRows[] = new TableSeparator();

        $tableRows[] = [
            'Total',
            $helper->humanCount($rows),
            $helper->humanCount($dataLengh),
            $helper->humanCount($indexLength),
        ];

        $table = new Table($output);
        $table
            ->setHeaders(['Table Name', 'Rows', 'Data Size', 'Index Size'])
            ->setRows($tableRows)
            ->render();

        return Command::SUCCESS;
    }
}
