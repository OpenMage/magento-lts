<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Install
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
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
