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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml order create gift message block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
    public function getFormHtml(Varien_Object $entity, $entityType='quote') {
        return $this->getLayout()->createBlock(
                    'adminhtml/sales_order_create_giftmessage_form'
               )->setEntity($entity)->setEntityType($entityType)->toHtml();
    }

    /**
     * Retrive items allowed for gift messages.
     *
     * If no items available return false.
     *
     * @return array|boolean
     */
    public function getItems()
    {
        $items = array();
        $allItems = $this->getQuote()->getAllItems();

        foreach ($allItems as $item) {
            if($this->_getGiftmessageSaveModel()->getIsAllowedQuoteItem($item)
               && $this->helper('giftmessage/message')->getIsMessagesAvailable('item',
                        $item, $this->getStore())) {
                // if item allowed
                $items[] = $item;
            }
        }

        if(sizeof($items)) {
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
