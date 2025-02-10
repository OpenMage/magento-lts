<?php
/**
 * Cache cleaner backend model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Rss
 */
class Mage_Rss_Model_System_Config_Backend_Links extends Mage_Core_Model_Config_Data
{
    /**
     * Invalidate cache type, when value was changed
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::app()->getCacheInstance()->invalidateType(Mage_Core_Block_Abstract::CACHE_GROUP);
        }
        return $this;
    }
}
