<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Model for working with system.xml module files
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Config_System extends Mage_Core_Model_Config_Base
{
    /**
     * @param string $module
     * @return $this
     */
    public function load($module)
    {
        $file = Mage::getConfig()->getModuleDir('etc', $module) . DS . 'system.xml';
        $this->loadFile($file);
        return $this;
    }
}
