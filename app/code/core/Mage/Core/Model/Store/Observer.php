<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Store observer
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Store_Observer
{
    public function cleanCache()
    {
        Mage::app()->cleanCache([Mage_Core_Model_Store::CACHE_TAG]);
    }
}
