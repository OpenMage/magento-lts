<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml new accounts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Customer_Accounts_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridAccounts');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/accounts_collection');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('accounts', [
            'header'    =>Mage::helper('reports')->__('Number of New Accounts'),
            'index'     =>'accounts',
            'total'     =>'sum',
            'type'      =>'number'
        ]);

        $this->addExportType('*/*/exportAccountsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportAccountsExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
