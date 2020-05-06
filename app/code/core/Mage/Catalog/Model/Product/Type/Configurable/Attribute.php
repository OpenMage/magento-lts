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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Configurable Product Attribute Model
 *
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute getResource()
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection getCollection()
 *
 * @method string getAttributeCode()
 * @method int getAttributeId()
 * @method $this setAttributeId(int $value)
 * @method $this setLabel(string $value)
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method array getPrices()
 * @method $this setPrices(array $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getProductAttribute()
 * @method $this setProductAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getUseDefault()
 * @method $this setUseDefault(int $value)
 * @method array getValues()
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Configurable_Attribute extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_type_configurable_attribute');
    }

    /**
     * Add price data to attribute
     *
     * @param array $priceData
     * @return $this
     */
    public function addPrice($priceData)
    {
        $data = $this->getPrices();
        if (is_null($data)) {
            $data = array();
        }
        $data[] = $priceData;
        $this->setPrices($data);
        return $this;
    }

    /**
     * Retrieve attribute label
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->getData('use_default') && $this->getProductAttribute()) {
            return $this->getProductAttribute()->getStoreLabel();
        } elseif (is_null($this->getData('label')) && $this->getProductAttribute()) {
            $this->setData('label', $this->getProductAttribute()->getStoreLabel());
        }

        return $this->getData('label');
    }

    /**
     * After save process
     *
     * @return $this
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->_getResource()->saveLabel($this);
        $this->_getResource()->savePrices($this);
        return $this;
    }
}
