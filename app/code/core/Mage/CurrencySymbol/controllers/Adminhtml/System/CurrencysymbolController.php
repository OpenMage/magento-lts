<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CurrencySymbol
 */

/**
 * Adminhtml Currency Symbols Controller
 *
 * @package    Mage_CurrencySymbol
 */
class Mage_CurrencySymbol_Adminhtml_System_CurrencysymbolController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/currency/symbols';

    /**
     * Show Currency Symbols Management dialog
     */
    public function indexAction()
    {
        // set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('system/currency/symbols')
            ->_addBreadcrumb(
                Mage::helper('currencysymbol')->__('System'),
                Mage::helper('currencysymbol')->__('System'),
            )
            ->_addBreadcrumb(
                Mage::helper('currencysymbol')->__('Manage Currency Rates'),
                Mage::helper('currencysymbol')->__('Manage Currency Rates'),
            );

        $this->_title($this->__('System'))
            ->_title($this->__('Manage Currency Rates'));
        $this->renderLayout();
    }

    /**
     * Save custom Currency symbol
     */
    public function saveAction()
    {
        $symbolsDataArray = $this->getRequest()->getParam('custom_currency_symbol', null);
        if (is_array($symbolsDataArray)) {
            foreach ($symbolsDataArray as &$symbolsData) {
                $symbolsData = Mage::helper('adminhtml')->stripTags($symbolsData);
            }
        }

        try {
            Mage::getModel('currencysymbol/system_currencysymbol')->setCurrencySymbolsData($symbolsDataArray);
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('currencysymbol')->__('Custom currency symbols were applied successfully.'),
            );
        } catch (Exception $exception) {
            Mage::getSingleton('adminhtml/session')->addError($exception->getMessage());
        }

        $this->_redirectReferer();
    }

    /**
     * Resets custom Currency symbol for all store views, websites and default value
     */
    public function resetAction()
    {
        Mage::getModel('currencysymbol/system_currencysymbol')->resetValues();
        $this->_redirectReferer();
    }
}
