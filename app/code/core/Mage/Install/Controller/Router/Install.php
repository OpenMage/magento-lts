<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Install
 * @author     Magento Core Team <core@magentocommerce.com>
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
