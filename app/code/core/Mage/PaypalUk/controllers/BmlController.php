<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_PaypalUk
 */

/**
 * Bill Me Later Controller
 *
 * @category   Mage
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
