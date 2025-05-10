<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Filter item model
 *
 * @package    Mage_Catalog
 *
 * @method int getCount()
 * @method $this setCount(int $value)
 * @method string getLabel()
 * @method $this setLabel(string $value)
 * @method string getValue()
 * @method $this setValue(string $value)
 * @method $this setFilter(Mage_Catalog_Model_Layer_Filter_Abstract $value)
 */
class Mage_Catalog_Model_Layer_Filter_Item extends Varien_Object
{
    /**
     * Get filter instance
     *
     * @return Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            Mage::throwException(
                Mage::helper('catalog')->__('Filter must be an object. Please set correct filter.'),
            );
        }
        return $filter;
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $query = [
            $this->getFilter()->getRequestVar() => $this->getValue(),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null, // exclude current page from urls
        ];
        return Mage::getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $query = [$this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue()];
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;
        return Mage::getUrl('*/*/*', $params);
    }

    /**
     * Get url for "clear" link
     *
     * @return false|string
     */
    public function getClearLinkUrl()
    {
        $clearLinkText = $this->getFilter()->getClearLinkText();
        if (!$clearLinkText) {
            return false;
        }

        $urlParams = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [$this->getFilter()->getRequestVar() => null],
            '_escape' => true,
        ];
        return Mage::getUrl('*/*/*', $urlParams);
    }

    /**
     * Get item filter name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFilter()->getName();
    }

    /**
     * Get item value as string
     *
     * @return string
     */
    public function getValueString()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }
}
