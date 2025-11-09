<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Selection Model
 *
 * @package    Mage_Bundle
 *
 * @method Mage_Bundle_Model_Resource_Selection _getResource()
 * @method Mage_Bundle_Model_Resource_Selection_Collection getCollection()
 * @method string getDefaultPriceScope()
 * @method int getIsDefault()
 * @method int getOptionId()
 * @method int getParentProductId()
 * @method int getPosition()
 * @method int getProductId()
 * @method Mage_Bundle_Model_Resource_Selection getResource()
 * @method Mage_Bundle_Model_Resource_Selection_Collection getResourceCollection()
 * @method int getSelectionCanChangeQty()
 * @method int getSelectionId()
 * @method int getSelectionPriceType()
 * @method float getSelectionPriceValue()
 * @method float getSelectionQty()
 * @method int getWebsiteId()
 * @method bool isSalable()
 * @method $this setIsDefault(int $value)
 * @method $this setOptionId(int $value)
 * @method $this setParentProductId(int $value)
 * @method $this setPosition(int $value)
 * @method $this setProductId(int $value)
 * @method $this setSelectionCanChangeQty(int $value)
 * @method $this setSelectionPriceType(int $value)
 * @method $this setSelectionPriceValue(float $value)
 * @method $this setSelectionQty(float $value)
 * @method $this setWebsiteId(int $value)
 * @method $this unsSelectionPriceType()
 * @method $this unsSelectionPriceValue()
 */
class Mage_Bundle_Model_Selection extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('bundle/selection');
        parent::_construct();
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _afterSave()
    {
        $storeId = Mage::registry('product')->getStoreId();
        if (!Mage::helper('catalog')->isPriceGlobal() && $storeId) {
            $this->setWebsiteId(Mage::app()->getStore($storeId)->getWebsiteId());
            $this->getResource()->saveSelectionPrice($this);

            if (!$this->getDefaultPriceScope()) {
                $this->unsSelectionPriceValue();
                $this->unsSelectionPriceType();
            }
        }

        return parent::_afterSave();
    }
}
