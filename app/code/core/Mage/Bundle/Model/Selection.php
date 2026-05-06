<?php

declare(strict_types=1);

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
 * @method Mage_Bundle_Model_Resource_Selection            _getResource()
 * @method Mage_Bundle_Model_Resource_Selection_Collection getCollection()
 * @method Mage_Bundle_Model_Resource_Selection            getResource()
 * @method Mage_Bundle_Model_Resource_Selection_Collection getResourceCollection()
 * @method bool                                            isSalable()
 * @method $this                                           unsSelectionPriceType()
 * @method $this                                           unsSelectionPriceValue()
 */
class Mage_Bundle_Model_Selection extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('bundle/selection');
        parent::_construct();
    }

    public function getIsDefault(): int
    {
        return (int) $this->_getData('is_default');
    }

    public function getOptionId(): int
    {
        return (int) $this->_getData('option_id');
    }

    public function getParentProductId(): int
    {
        return (int) $this->_getData('parent_product_id');
    }

    public function getPosition(): int
    {
        return (int) $this->_getData('position');
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function getSelectionCanChangeQty(): int
    {
        return (int) $this->_getData('selection_can_change_qty');
    }

    public function getSelectionPriceType(): ?int
    {
        $value = $this->_getData('selection_price_type');
        return $value !== null ? (int) $value : null;
    }

    public function getSelectionPriceValue(): ?float
    {
        $value = $this->_getData('selection_price_value');
        return $value !== null ? (float) $value : null;
    }

    public function getSelectionQty(): ?float
    {
        $value = $this->_getData('selection_qty');
        return $value !== null ? (float) $value : null;
    }

    public function setIsDefault(int $value): static
    {
        return $this->setData('is_default', $value);
    }

    public function setOptionId(int $value): static
    {
        return $this->setData('option_id', $value);
    }

    public function setParentProductId(int $value): static
    {
        return $this->setData('parent_product_id', $value);
    }

    public function setPosition(int $value): static
    {
        return $this->setData('position', $value);
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function setSelectionCanChangeQty(int $value): static
    {
        return $this->setData('selection_can_change_qty', $value);
    }

    public function setSelectionPriceType(?int $value): static
    {
        return $this->setData('selection_price_type', $value);
    }

    public function setSelectionPriceValue(?float $value): static
    {
        return $this->setData('selection_price_value', $value);
    }

    public function setSelectionQty(?float $value): static
    {
        return $this->setData('selection_qty', $value);
    }

    public function getDefaultPriceScope(): string
    {
        return (string) $this->_getData('default_price_scope');
    }

    public function getSelectionId(): int
    {
        return (int) $this->_getData('selection_id');
    }

    public function getWebsiteId(): int
    {
        return (int) $this->_getData('website_id');
    }

    public function setWebsiteId(int $value): static
    {
        return $this->setData('website_id', $value);
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Model_Store_Exception
     */
    #[Override]
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
