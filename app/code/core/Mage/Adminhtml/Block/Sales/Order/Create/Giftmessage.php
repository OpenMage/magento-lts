<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml order create gift message block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Giftmessage extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Generate form for editing of gift message for entity
     *
     * @param Varien_Object $entity
     * @param string        $entityType
     * @return string
     */
    public function getFormHtml(Varien_Object $entity, $entityType = 'quote')
    {
        return $this->getLayout()->createBlock(
            'adminhtml/sales_order_create_giftmessage_form'
        )->setEntity($entity)->setEntityType($entityType)->toHtml();
    }

    /**
     * Retrieve items allowed for gift messages.
     *
     * If no items available return false.
     *
     * @return array|bool
     */
    public function getItems()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');

        $items = [];
        $allItems = $this->getQuote()->getAllItems();

        foreach ($allItems as $item) {
            if ($this->_getGiftmessageSaveModel()->getIsAllowedQuoteItem($item)
                && $helper->getIsMessagesAvailable('item', $item, $this->getStore())) {
                // if item allowed
                $items[] = $item;
            }
        }

        if (count($items)) {
            return $items;
        }

        return false;
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
