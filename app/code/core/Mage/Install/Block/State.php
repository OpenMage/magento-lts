<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Install state block
 *
 * @package    Mage_Install
 */
class Mage_Install_Block_State extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('install/state.phtml');
        $this->assign('steps', Mage::getSingleton('install/wizard')->getSteps());
    }
}
