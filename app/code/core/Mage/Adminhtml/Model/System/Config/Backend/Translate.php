<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * System config translate inline fields backend model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Translate extends Mage_Core_Model_Config_Data
{
    /**
     * Path to config node with list of caches
     *
     * @var string
     */
    public const XML_PATH_INVALID_CACHES = 'dev/translate_inline/invalid_caches';

    /**
     * Set status 'invalidate' for blocks and other output caches
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $types = array_keys(Mage::getStoreConfig(self::XML_PATH_INVALID_CACHES));
        if ($this->isValueChanged()) {
            Mage::app()->getCacheInstance()->invalidateType($types);
        }

        return $this;
    }
}
