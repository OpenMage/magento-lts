<?php

/**
 * @category   Mage
 * @package    Mage_PaypalUk
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
