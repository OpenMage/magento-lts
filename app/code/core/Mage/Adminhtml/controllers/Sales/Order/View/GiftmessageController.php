<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order view gift messages controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Sales_Order_View_GiftmessageController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/order';

    /**
     * Additional initialization
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    public function saveAction()
    {
        try {
            $this->_getGiftmessageSaveModel()
                ->setGiftmessages($this->getRequest()->getParam('giftmessage'))
                ->saveAllInOrder();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(Mage::helper('giftmessage')->__('An error occurred while saving the gift message.'));
        }

        if ($this->getRequest()->getParam('type') == 'order_item') {
            $this->getResponse()->setBody(
                $this->_getGiftmessageSaveModel()->getSaved() ? 'YES' : 'NO',
            );
        } else {
            $this->getResponse()->setBody(
                Mage::helper('giftmessage')->__('The gift message has been saved.'),
            );
        }
    }

    /**
     * Retrieve gift message save model
     *
     * @return Mage_Adminhtml_Model_Giftmessage_Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return Mage::getSingleton('adminhtml/giftmessage_save');
    }
}
