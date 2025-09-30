<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Install index controller
 *
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
