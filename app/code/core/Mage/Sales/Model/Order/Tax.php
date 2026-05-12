<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Tax            _getResource()
 * @method Mage_Sales_Model_Resource_Order_Tax_Collection getCollection()
 * @method Mage_Sales_Model_Resource_Order_Tax            getResource()
 * @method Mage_Sales_Model_Resource_Order_Tax_Collection getResourceCollection()
 */
class Mage_Sales_Model_Order_Tax extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_tax');
    }

    public function getAmount(): float
    {
        return (float) $this->_getData('amount');
    }

    public function setAmount(float $value): static
    {
        return $this->setData('amount', $value);
    }

    public function getBaseAmount(): float
    {
        return (float) $this->_getData('base_amount');
    }

    public function setBaseAmount(float $value): static
    {
        return $this->setData('base_amount', $value);
    }

    public function getBaseRealAmount(): float
    {
        return (float) $this->_getData('base_real_amount');
    }

    public function setBaseRealAmount(float $value): static
    {
        return $this->setData('base_real_amount', $value);
    }

    public function getCode(): string
    {
        return (string) $this->_getData('code');
    }

    public function setCode(string $value): static
    {
        return $this->setData('code', $value);
    }

    public function getHidden(): int
    {
        return (int) $this->_getData('hidden');
    }

    public function setHidden(int $value): static
    {
        return $this->setData('hidden', $value);
    }

    public function getOrderId(): int
    {
        return (int) $this->_getData('order_id');
    }

    public function setOrderId(int $value): static
    {
        return $this->setData('order_id', $value);
    }

    public function getPercent(): ?float
    {
        $value = $this->_getData('percent');
        return $value !== null ? (float) $value : null;
    }

    public function setPercent(float $value): static
    {
        return $this->setData('percent', $value);
    }

    public function getPosition(): int
    {
        return (int) $this->_getData('position');
    }

    public function setPosition(int $value): static
    {
        return $this->setData('position', $value);
    }

    public function getPriority(): int
    {
        return (int) $this->_getData('priority');
    }

    public function setPriority(int $value): static
    {
        return $this->setData('priority', $value);
    }

    public function getProcess(): int
    {
        return (int) $this->_getData('process');
    }

    public function setProcess(int $value): static
    {
        return $this->setData('process', $value);
    }

    public function getTitle(): string
    {
        return (string) $this->_getData('title');
    }

    public function setTitle(string $value): static
    {
        return $this->setData('title', $value);
    }
}
