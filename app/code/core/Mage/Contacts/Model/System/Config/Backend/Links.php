<?php
/**
 * Cache cleaner backend model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Contacts
 */
class Mage_Contacts_Model_System_Config_Backend_Links extends Mage_Adminhtml_Model_System_Config_Backend_Cache
{
    /**
     * Cache tags to clean
     * @var array
     */
    protected $_cacheTags = [Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG];
}
