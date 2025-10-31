<?php

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
 * @method Mage_Cms_Model_Resource_Block _getResource()
 * @method Mage_Cms_Model_Resource_Block getResource()
 * @method Mage_Cms_Model_Resource_Block_Collection getCollection()
 */
class Mage_Cms_Model_Block extends Mage_Core_Model_Abstract implements Mage_Cms_Api_Data_BlockInterface
{
    public const CACHE_TAG  = 'cms_block';

    protected $_cacheTag    = 'cms_block';

    protected function _construct()
    {
        $this->_init('cms/block');
    }

    /**
     * Prevent blocks recursion
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
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

    /**
     * @api
     */
    public function getBlockId(): ?int
    {
        $blockId = $this->getDataByKey(self::DATA_ID);
        return is_null($blockId) ? null : (int) $blockId;
    }

    /**
     * @api
     * @return $this
     */
    public function setBlockId(?int $blockId)
    {
        return $this->setData(self::DATA_ID, $blockId);
    }

    /**
     * @api
     */
    public function getContent(): ?string
    {
        return $this->getDataByKey(self::DATA_CONTENT);
    }

    /**
     * @api
     * @return $this
     */
    public function setContent(?string $content)
    {
        return $this->setData(self::DATA_CONTENT, $content);
    }

    /**
     * @api
     */
    public function getCreationTime(): ?string
    {
        return $this->getDataByKey(self::DATA_CREATION_TIME);
    }

    /**
     * @api
     * @return $this
     */
    public function setCreationTime(?string $time)
    {
        return $this->setData(self::DATA_CREATION_TIME, $time);
    }

    /**
     * @api
     */
    public function getIsActive(): int
    {
        return $this->getDataByKey(self::DATA_IS_ACTIVE);
    }

    /**
     * @api
     * @return $this
     */
    public function setIsActive(int $value)
    {
        return $this->setData(self::DATA_IS_ACTIVE, $value);
    }

    /**
     * @api
     */
    public function getIdentifier(): string
    {
        return $this->getDataByKey(self::DATA_IDENTIFIER);
    }

    /**
     * @api
     * @return $this
     */
    public function setIdentifier(string $identifier)
    {
        return $this->setData(self::DATA_IDENTIFIER, $identifier);
    }

    /**
     * @api
     */
    public function getTitle(): string
    {
        return $this->getDataByKey(self::DATA_TITLE);
    }

    /**
     * @api
     * @return $this
     */
    public function setTitle(string $title)
    {
        return $this->setData(self::DATA_TITLE, $title);
    }

    /**
     * @api
     */
    public function getStoreId(): ?int
    {
        return $this->getDataByKey(self::DATA_STORE_ID);
    }

    /**
     * @api
     * @return $this
     */
    public function setStoreId(int $storeId)
    {
        return $this->setData(self::DATA_STORE_ID, $storeId);
    }

    /**
     * @api
     */
    public function getUpdateTime(): ?string
    {
        return $this->getDataByKey(self::DATA_UPDATE_TIME);
    }

    /**
     * @api
     * @return $this
     */
    public function setUpdateTime(?string $time)
    {
        return $this->setData(self::DATA_UPDATE_TIME, $time);
    }
}
