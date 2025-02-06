<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Bill Me Later Controller
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_BmlController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action for Bill Me Later checkout button (product view and shopping cart pages)
     */
    public function startAction()
    {
        $this->_forward('start', 'express', 'paypal', [
            'bml' => 1,
            'button' => $this->getRequest()->getParam('button'),
        ]);
    }
}
