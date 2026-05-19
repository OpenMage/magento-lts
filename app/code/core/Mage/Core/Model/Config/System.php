<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Model for working with system.xml module files
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Config_System extends Mage_Core_Model_Config_Base
{
    /**
     * @param  string $module
     * @return $this
     */
    public function load($module)
    {
        $file = Mage::getConfig()->getModuleDir('etc', $module) . DS . 'system.xml';
        $this->loadFile($file);
        return $this;
    }
}
