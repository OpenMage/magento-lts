<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Abstract installation block
 *
 * @package    Mage_Install
 */
abstract class Mage_Install_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * Retrieve installer model
     *
     * @return Mage_Install_Model_Installer
     */
    public function getInstaller()
    {
        return Mage::getSingleton('install/installer');
    }

    /**
     * Retrieve wizard model
     *
     * @return Mage_Install_Model_Wizard
     */
    public function getWizard()
    {
        return Mage::getSingleton('install/wizard');
    }

    /**
     * Retrieve current installation step
     *
     * @return Varien_Object
     */
    public function getCurrentStep()
    {
        return $this->getWizard()->getStepByRequest($this->getRequest());
    }
}
