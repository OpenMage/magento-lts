<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Adminhtml mobile dashboard controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Adminhtml_Connect_DashboardController extends Mage_XmlConnect_Controller_AdminAction
{
    /**
     * Dashboard index action
     */
    public function indexAction()
    {
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $this->_message(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SPACE_DEFAULT, $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT,
                $this->__('An error occurred while loading configuration.')
            );
        }
    }
}
