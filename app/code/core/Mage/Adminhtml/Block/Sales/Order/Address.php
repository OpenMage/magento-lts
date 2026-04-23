<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Edit order address form container block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Address extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'sales_order';
        $this->_mode       = 'address';
        parent::__construct();
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', Mage::helper('sales')->__('Save Order Address'));
        $this->_removeButton(self::BUTTON_TYPE_DELETE);
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        $address = Mage::registry('order_address');
        $orderId = $address->getOrder()->getIncrementId();
        if ($address->getAddressType() == 'shipping') {
            $type = Mage::helper('sales')->__('Shipping');
        } else {
            $type = Mage::helper('sales')->__('Billing');
        }

        return Mage::helper('sales')->__('Edit Order %s %s Address', $orderId, $type);
    }

    /**
     * Back button url getter
     *
     * @return string
     */
    #[Override]
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/*/view',
            ['order_id' => Mage::registry('order_address')->getOrder()->getId()],
        );
    }
}
