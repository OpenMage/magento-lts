<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Store observer
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Store_Observer
{
    public function cleanCache()
    {
        Mage::app()->cleanCache([Mage_Core_Model_Store::CACHE_TAG]);
    }
}
