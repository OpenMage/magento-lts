<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * Advanced search form
 *
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Block_Advanced_Form extends Mage_Core_Block_Template
{
    /**
     * @return Mage_Core_Block_Template
     */
    public function _prepareLayout()
    {
        // add Home breadcrumb
        /** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb('home', [
                'label' => Mage::helper('catalogsearch')->__('Home'),
                'title' => Mage::helper('catalogsearch')->__('Go to Home Page'),
                'link' => Mage::getBaseUrl(),
            ])->addCrumb('search', [
                'label' => Mage::helper('catalogsearch')->__('Catalog Advanced Search'),
            ]);
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve collection of product searchable attributes
     *
     * @return Varien_Data_Collection_Db
     */
    public function getSearchableAttributes()
    {
        return $this->getModel()->getAttributes();
    }

    /**
     * Retrieve attribute label
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeLabel($attribute)
    {
        return $attribute->getStoreLabel();
    }

    /**
     * Retrieve attribute input validation class
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeValidationClass($attribute)
    {
        return $attribute->getFrontendClass();
    }

    /**
     * Retrieve search string for given field from request
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param null|string $part
     * @return mixed|string
     */
    public function getAttributeValue($attribute, $part = null)
    {
        $value = $this->getRequest()->getQuery($attribute->getAttributeCode());
        if ($part && $value) {
            $value = $value[$part] ?? '';
        }

        return $value;
    }

    /**
     * Retrieve the list of available currencies
     *
     * @return array
     */
    public function getAvailableCurrencies()
    {
        $currencies = $this->getData('_currencies');
        if (is_null($currencies)) {
            $currencies = [];
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes)) {
                $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                    Mage::app()->getStore()->getBaseCurrency(),
                    $codes,
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = $code;
                    }
                }
            }

            $this->setData('currencies', $currencies);
        }

        return $currencies;
    }

    /**
     * Count available currencies
     *
     * @return int
     */
    public function getCurrencyCount()
    {
        return count($this->getAvailableCurrencies());
    }

    /**
     * Retrieve currency code for attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getCurrency($attribute)
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Retrieve attribute input type
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return  string
     */
    public function getAttributeInputType($attribute)
    {
        $dataType   = $attribute->getBackend()->getType();
        $imputType  = $attribute->getFrontend()->getInputType();
        if ($imputType == 'select' || $imputType == 'multiselect') {
            return 'select';
        }

        if ($imputType == 'boolean') {
            return 'yesno';
        }

        if ($imputType == 'price') {
            return 'price';
        }

        if ($dataType == 'int' || $dataType == 'decimal') {
            return 'number';
        }

        if ($dataType == 'datetime') {
            return 'date';
        }

        return 'string';
    }

    /**
     * Build attribute select element html string
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeSelectElement($attribute)
    {
        $extra = '';
        $options = $attribute->getSource()->getAllOptions(false);

        $name = $attribute->getAttributeCode();

        // 2 - avoid yes/no selects to be multiselects
        if (is_array($options) && count($options) > 2) {
            $extra = 'multiple="multiple" size="4"';
            $name .= '[]';
        } else {
            array_unshift($options, ['value' => '', 'label' => Mage::helper('catalogsearch')->__('All')]);
        }

        return $this->_getSelectBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams($extra)
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
            ->setClass('multiselect')
            ->getHtml();
    }

    /**
     * Retrieve yes/no element html for provided attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeYesNoElement($attribute)
    {
        $options = [
            ['value' => '',  'label' => Mage::helper('catalogsearch')->__('All')],
            ['value' => '1', 'label' => Mage::helper('catalogsearch')->__('Yes')],
            ['value' => '0', 'label' => Mage::helper('catalogsearch')->__('No')],
        ];

        $name = $attribute->getAttributeCode();
        return $this->_getSelectBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams('')
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
            ->getHtml();
    }

    /**
     * @return Mage_Core_Block_Abstract|Mage_Core_Block_Html_Select
     */
    protected function _getSelectBlock()
    {
        $block = $this->getData('_select_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('core/html_select');
            $this->setData('_select_block', $block);
        }

        return $block;
    }

    /**
     * @return Mage_Core_Block_Abstract|Mage_Core_Block_Html_Date
     */
    protected function _getDateBlock()
    {
        $block = $this->getData('_date_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('core/html_date');
            $this->setData('_date_block', $block);
        }

        return $block;
    }

    /**
     * Retrieve advanced search model object
     *
     * @return Mage_CatalogSearch_Model_Advanced
     */
    public function getModel()
    {
        return Mage::getSingleton('catalogsearch/advanced');
    }

    /**
     * Retrieve search form action url
     *
     * @return string
     */
    public function getSearchPostUrl()
    {
        return $this->getUrl('*/*/result', ['_secure' => $this->_isSecure()]);
    }

    /**
     * Build date element html string for attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string $part
     * @return string
     */
    public function getDateInput($attribute, $part = 'from')
    {
        $name = $attribute->getAttributeCode() . '[' . $part . ']';
        $value = $this->getAttributeValue($attribute, $part);

        return $this->_getDateBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode() . ($part == 'from' ? '' : '_' . $part))
            ->setTitle($this->getAttributeLabel($attribute))
            ->setValue($value)
            ->setImage($this->getSkinUrl('images/calendar.gif'))
            ->setFormat('%m/%d/%y')
            ->setClass('input-text')
            ->getHtml();
    }
}
