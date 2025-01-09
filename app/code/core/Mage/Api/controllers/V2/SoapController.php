<?php

/**
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice main controller
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_V2_SoapController extends Mage_Api_Controller_Action
{
    public function indexAction()
    {
        if (Mage::helper('api/data')->isComplianceWSI()) {
            $handlerName = 'soap_wsi';
        } else {
            $handlerName = 'soap_v2';
        }

        $this->_getServer()->init($this, $handlerName, $handlerName)->run();
    }
}
