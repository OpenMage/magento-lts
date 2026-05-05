<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * @package    Mage_Checkout
 *
 * @method Mage_Checkout_Model_Resource_Agreement            _getResource()
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getCollection()
 * @method Mage_Checkout_Model_Resource_Agreement            getResource()
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getResourceCollection()
 * @method int                                               getStoreId()
 */
class Mage_Checkout_Model_Agreement extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('checkout/agreement');
    }

    public function getCheckboxText(): string
    {
        return (string) $this->_getData('checkbox_text');
    }

    public function getContent(): string
    {
        return (string) $this->_getData('content');
    }

    public function getContentHeight(): string
    {
        return (string) $this->_getData('content_height');
    }

    public function getIsActive(): int
    {
        return (int) $this->_getData('is_active');
    }

    public function getIsHtml(): int
    {
        return (int) $this->_getData('is_html');
    }

    public function getName(): string
    {
        return (string) $this->_getData('name');
    }

    public function setCheckboxText(string $value): static
    {
        return $this->setData('checkbox_text', $value);
    }

    public function setContent(string $value): static
    {
        return $this->setData('content', $value);
    }

    public function setContentHeight(string $value): static
    {
        return $this->setData('content_height', $value);
    }

    public function setIsActive(int $value): static
    {
        return $this->setData('is_active', $value);
    }

    public function setIsHtml(int $value): static
    {
        return $this->setData('is_html', $value);
    }

    public function setName(string $value): static
    {
        return $this->setData('name', $value);
    }
}
