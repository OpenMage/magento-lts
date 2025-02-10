<?php
/**
 * Xml Rpc webservice controller
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
