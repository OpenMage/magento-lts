<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
class Mage_Core_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_forward('noRoute');
    }
}
