<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * @package    Mage_Install
 */
class Mage_Install_Controller_Router_Install extends Mage_Core_Controller_Varien_Router_Standard
{
    /**
     * Check if current controller instance is allowed in current router.
     *
     * @param Mage_Core_Controller_Varien_Action $controllerInstance
     * @return bool
     */
    protected function _validateControllerInstance($controllerInstance)
    {
        return $controllerInstance instanceof Mage_Install_Controller_Action;
    }
}
