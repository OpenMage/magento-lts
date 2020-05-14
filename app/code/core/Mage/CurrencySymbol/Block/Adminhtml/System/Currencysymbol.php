<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CurrencySymbol
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage currency symbols block
 *
 * @category   Mage
 * @package    Mage_CurrencySymbol
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @property string $_blockGroup
 */
class Mage_CurrencySymbol_Block_Adminhtml_System_Currencysymbol extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @var string
     */
    private $_controller;

    /**
     * Constructor. Initialization required variables for class instance.
     */
    public function __construct()
    {
        $this->_blockGroup = 'currencysymbol_system';
        $this->_controller = 'adminhtml_system_currencysymbol';
        parent::__construct();
    }

    /**
     * Custom currency symbol properties
     *
     * @var array
     */
    protected $_symbolsData = array();

    /**
     * Prepares layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Returns page header
     *
     * @return bool|string
     */
    public function getHeader()
    {
        return Mage::helper('adminhtml')->__('Manage Currency Symbols');
    }

    /**
     * Returns 'Save Currency Symbol' button's HTML code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        /** @var Mage_Core_Block_Abstract $block */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        $block->setData(array(
            'label'     => Mage::helper('currencysymbol')->__('Save Currency Symbols'),
            'onclick'   => 'currencySymbolsForm.submit();',
            'class'     => 'save'
        ));

        return $block->toHtml();
    }

    /**
     * Returns URL for save action
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Returns website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getRequest()->getParam('website');
    }

    /**
     * Returns store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }

    /**
     * Returns Custom currency symbol properties
     *
     * @return array
     */
    public function getCurrencySymbolsData()
    {
        if (!$this->_symbolsData) {
            $this->_symbolsData =  Mage::getModel('currencysymbol/system_currencysymbol')
                ->getCurrencySymbolsData();
        }
        return $this->_symbolsData;
    }

    /**
     * Returns inheritance text
     *
     * @return string
     */
    public function getInheritText()
    {
        return Mage::helper('currencysymbol')->__('Use Standard');
    }
}
