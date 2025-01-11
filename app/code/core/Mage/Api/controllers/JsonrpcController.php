<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2023-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice main controller
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_JsonrpcController extends Mage_Api_Controller_Action
{
    public function indexAction()
    {
        $this->_getServer()->init($this, 'jsonrpc')
            ->run();
    }
}
