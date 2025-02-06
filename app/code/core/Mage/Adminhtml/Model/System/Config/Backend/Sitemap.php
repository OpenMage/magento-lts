<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Sitemap extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value < 0 || $value > 1) {
            throw new Exception(Mage::helper('sitemap')->__('The priority must be between 0 and 1.'));
        } elseif (($value == 0) && !($value === '0' || $value === '0.0')) {
            throw new Exception(Mage::helper('sitemap')->__('The priority must be between 0 and 1.'));
        }
        return $this;
    }
}
