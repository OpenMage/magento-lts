<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Base api controller
 *
 * @package    Mage_Api
*/
class Mage_Api_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * @return $this
     */
    public function preDispatch()
    {
        $this->getLayout()->setArea('adminhtml');
        Mage::app()->setCurrentStore('admin');
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1); // Do not start standard session
        parent::preDispatch();
        return $this;
    }

    /**
     * Retrieve webservice server
     *
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('api/server');
    }
}
