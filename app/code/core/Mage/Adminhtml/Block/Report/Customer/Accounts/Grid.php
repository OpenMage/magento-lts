<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml new accounts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Customer_Accounts_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridAccounts');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/accounts_collection');
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('accounts', [
            'header'    => Mage::helper('reports')->__('Number of New Accounts'),
            'index'     => 'accounts',
            'total'     => 'sum',
            'type'      => 'number',
        ]);

        $this->addExportType('*/*/exportAccountsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportAccountsExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
