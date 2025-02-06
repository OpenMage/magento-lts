<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * System config symlink backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Symlink extends Mage_Core_Model_Config_Data
{
    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        return $this;
    }
}
