<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Manage currency block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Currency extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('system/currency/rates.phtml');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Save Currency Rates'),
                    'onclick'   => 'currencyForm.submit();',
                    'class'     => 'save',
                ]),
        );

        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Reset'),
                    'onclick'   => 'document.location.reload()',
                    'class'     => 'reset',
                ]),
        );

        $this->setChild(
            'import_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Import'),
                    'class'     => 'add import',
                    'type'      => 'submit',
                ]),
        );

        $this->setChild(
            'rates_matrix',
            $this->getLayout()->createBlock('adminhtml/system_currency_rate_matrix'),
        );

        $this->setChild(
            'import_services',
            $this->getLayout()->createBlock('adminhtml/system_currency_rate_services'),
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return Mage::helper('adminhtml')->__('Manage Currency Rates');
    }

    /**
     * @return string
     */
    protected function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     */
    protected function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * @return string
     */
    protected function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
    }

    /**
     * @return string
     */
    protected function getServicesHtml()
    {
        return $this->getChildHtml('import_services');
    }

    /**
     * @return string
     */
    protected function getRatesMatrixHtml()
    {
        return $this->getChildHtml('rates_matrix');
    }

    /**
     * @return string
     */
    protected function getImportFormAction()
    {
        return $this->getUrl('*/*/fetchRates');
    }
}
