<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * CMS block model
 *
 * @package    Mage_Cms
 *
 * @method Mage_Cms_Model_Resource_Block            _getResource()
 * @method Mage_Cms_Model_Resource_Block_Collection getCollection()
 * @method Mage_Cms_Model_Resource_Block            getResource()
 * @method Mage_Cms_Model_Resource_Block_Collection getResourceCollection()
 * @method int                                      getStoreId()
 * @method $this                                    setStoreId(int $storeId)
 */
class Mage_Cms_Model_Block extends Mage_Core_Model_Abstract
{
    public const CACHE_TAG     = 'cms_block';

    protected $_cacheTag = 'cms_block';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('cms/block');
    }

    public function getBlockId(): int
    {
        return (int) $this->_getData('block_id');
    }

    public function getContent(): string
    {
        return (string) $this->_getData('content');
    }

    public function getCreationTime(): ?string
    {
        $v = $this->_getData('creation_time');
        return $v !== null ? (string) $v : null;
    }

    public function getIdentifier(): string
    {
        return (string) $this->_getData('identifier');
    }

    public function getIsActive(): int
    {
        return (int) $this->_getData('is_active');
    }

    public function getTitle(): string
    {
        return (string) $this->_getData('title');
    }

    public function getUpdateTime(): ?string
    {
        $v = $this->_getData('update_time');
        return $v !== null ? (string) $v : null;
    }

    public function setContent(string $value): static
    {
        return $this->setData('content', $value);
    }

    public function setCreationTime(?string $value): static
    {
        return $this->setData('creation_time', $value);
    }

    public function setIdentifier(string $value): static
    {
        return $this->setData('identifier', $value);
    }

    public function setIsActive(int $value): static
    {
        return $this->setData('is_active', $value);
    }

    public function setTitle(string $value): static
    {
        return $this->setData('title', $value);
    }

    public function setUpdateTime(?string $value): static
    {
        return $this->setData('update_time', $value);
    }

    /**
     * Prevent blocks recursion
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
    protected function _beforeSave()
    {
        $needle = 'block_id="' . $this->getBlockId() . '"';
        if (!strstr($this->getContent(), $needle)) {
            return parent::_beforeSave();
        }

        Mage::throwException(
            Mage::helper('cms')->__('The static block content cannot contain  directive with its self.'),
        );
    }
}
