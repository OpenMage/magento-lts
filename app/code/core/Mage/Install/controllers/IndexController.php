<?php

/**
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
