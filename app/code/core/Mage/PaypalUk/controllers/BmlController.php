<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 * Bill Me Later Controller
 *
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_BmlController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action for Bill Me Later checkout button (product view and shopping cart pages)
     */
    public function startAction()
    {
        $this->_forward('start', 'express', 'payflow', ['bml' => 1]);
    }
}
