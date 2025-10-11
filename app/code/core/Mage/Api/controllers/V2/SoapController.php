<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservice main controller
 *
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
