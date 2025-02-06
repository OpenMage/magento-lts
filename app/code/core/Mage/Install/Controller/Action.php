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
class Mage_Install_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    protected function _construct()
    {
        parent::_construct();

        Mage::getDesign()->setArea('install')
            ->setPackageName('default')
            ->setTheme('default');

        $this->getLayout()->setArea('install');

        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
    }
}
