<?php
/**
 * Webservice main controller
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
