<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Xml Rpc webservice controller
 *
 * @package    Mage_Api
 */
class Mage_Api_XmlrpcController extends Mage_Api_Controller_Action
{
    public function indexAction()
    {
        $this->_getServer()->init($this, 'xmlrpc')
            ->run();
    }
}
