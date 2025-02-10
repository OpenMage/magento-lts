<?php
/**
 * Install state block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
