<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * Webservice main controller
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_IndexController extends Mage_Api_Controller_Action
{
    public function indexAction()
    {
        $this->_getServer()->init($this)->run();
    }
}
