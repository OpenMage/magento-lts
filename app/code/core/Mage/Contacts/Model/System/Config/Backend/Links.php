<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Contacts
 */

/**
 * Cache cleaner backend model
 *
 * @category   Mage
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
