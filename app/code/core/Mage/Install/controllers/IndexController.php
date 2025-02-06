<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Install
 */

/**
 * Install index controller
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_IndexController extends Mage_Install_Controller_Action
{
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, true);
        parent::preDispatch();
    }

    public function indexAction()
    {
        $this->_forward('begin', 'wizard', 'install');
    }
}
