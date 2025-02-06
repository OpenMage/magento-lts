<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Install
 */

/**
 * Install state block
 *
 * @category   Mage
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
