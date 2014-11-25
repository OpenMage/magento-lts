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
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml weee tax item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Weee_Block_Renderer_Weee_Tax extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Object being rendered
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element = null;

    /**
     * List of countries
     *
     * @var array
     */
    protected $_countries = null;

    /**
     * List of websites
     *
     * @var array
     */
    protected $_websites = null;

    /**
     * Public constructor
     */
    public function __construct()
    {
        $this->setTemplate('weee/renderer/tax.phtml');
    }

    /**
     * Retrieve product in question
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Renders html of block
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $this->_setAddButton();
        return $this->toHtml();
    }

    /**
     * Sets internal reference to element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return Mage_Weee_Block_Renderer_Weee_Tax
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieves element
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Retrieves list of values
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $data = $this->getElement()->getValue();

        if (is_array($data) && count($data)) {
            usort($data, array($this, '_sortWeeeTaxes'));
            $values = $data;
        }
        return $values;
    }

    /**
     * Sorts Weee Taxes
     *
     * @param array $a
     * @param array $b
     *
     * @return integer
     */
    protected function _sortWeeeTaxes($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }
        if ($a['country'] != $b['country']) {
            return $a['country'] < $b['country'] ? -1 : 1;
        }
        return 0;
    }

    /**
     * Retrieves number of websites
     *
     * @return integer
     */
    public function getWebsiteCount()
    {
        return count($this->getWebsites());
    }

    /**
     * Is multi websites?
     *
     * @return bool
     */
    public function isMultiWebsites()
    {
        return !Mage::app()->isSingleStoreMode();
    }

    /**
     * Get list of countries
     *
     * @return array
     */
    public function getCountries()
    {
        if (is_null($this->_countries)) {
            $this->_countries = Mage::getModel('adminhtml/system_config_source_country')
                ->toOptionArray();
        }

        return $this->_countries;
    }

    /**
     * Get list of websites
     *
     * @return array
     */
    public function getWebsites()
    {
        if (!is_null($this->_websites)) {
            return $this->_websites;
        }
        $websites = array();
        $websites[0] = array(
            'name' => $this->__('All Websites'),
            'currency' => Mage::app()->getBaseCurrencyCode()
        );

        if (!Mage::app()->isSingleStoreMode() && !$this->getElement()->getEntityAttribute()->isScopeGlobal()) {
            if ($storeId = $this->getProduct()->getStoreId()) {
                $website = Mage::app()->getStore($storeId)->getWebsite();
                $websites[$website->getId()] = array(
                    'name' => $website->getName(),
                    'currency' => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                );
            } else {
                foreach (Mage::app()->getWebsites() as $website) {
                    if (!in_array($website->getId(), $this->getProduct()->getWebsiteIds())) {
                        continue;
                    }
                    $websites[$website->getId()] = array(
                        'name' => $website->getName(),
                        'currency' => $website->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
                    );
                }
            }
        }
        $this->_websites = $websites;
        return $this->_websites;
    }

    /**
     * Set add button and its properties
     */
    protected function _setAddButton()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array('id' => "add_tax_" . $this->getElement()->getHtmlId(),
                'label' => Mage::helper('catalog')->__('Add Tax'),
                'onclick' => "weeeTaxControl.addItem('" . $this->getElement()->getHtmlId() . "')",
                'class' => 'add'
            )));
    }

    /**
     * Retrieve add button html
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}

