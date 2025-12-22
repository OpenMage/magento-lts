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
 * @method Mage_Cms_Model_Resource_Block            _getResource()
 * @method int                                      getBlockId()
 * @method Mage_Cms_Model_Resource_Block_Collection getCollection()
 * @method string                                   getContent()
 * @method string                                   getCreationTime()
 * @method string                                   getIdentifier()
 * @method int                                      getIsActive()
 * @method Mage_Cms_Model_Resource_Block            getResource()
 * @method Mage_Cms_Model_Resource_Block_Collection getResourceCollection()
 * @method int                                      getStoreId()
 * @method string                                   getTitle()
 * @method string                                   getUpdateTime()
 * @method $this                                    setContent(string $value)
 * @method $this                                    setCreationTime(string $value)
 * @method $this                                    setIdentifier(string $value)
 * @method $this                                    setIsActive(int $value)
 * @method $this                                    setStoreId(int $storeId)
 * @method $this                                    setTitle(string $value)
 * @method $this                                    setUpdateTime(string $value)
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

    /**
     * Prevent blocks recursion
     *
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
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
}
