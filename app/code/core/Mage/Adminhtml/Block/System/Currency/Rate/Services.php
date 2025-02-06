<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Manage currency import services block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Currency_Rate_Services extends Mage_Adminhtml_Block_Template
{
    /**
     * Set import services template
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('system/currency/rate/services.phtml');
    }

    /**
     * Create import services form select element
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'import_services',
            $this->getLayout()->createBlock('adminhtml/html_select')
            ->setOptions(Mage::getModel('adminhtml/system_config_source_currency_service')->toOptionArray(0))
            ->setId('rate_services')
            ->setName('rate_services')
            ->setValue(Mage::getSingleton('adminhtml/session')->getCurrencyRateService(true))
            ->setTitle(Mage::helper('adminhtml')->__('Import Service')),
        );

        return parent::_prepareLayout();
    }
}
