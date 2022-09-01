<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base api controller
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
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
