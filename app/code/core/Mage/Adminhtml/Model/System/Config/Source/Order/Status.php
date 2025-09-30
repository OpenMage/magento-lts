<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Order Statuses source model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Order_Status
{
    // set null to enable all possible
    protected $_stateStatuses = [
        Mage_Sales_Model_Order::STATE_NEW,
        Mage_Sales_Model_Order::STATE_PROCESSING,
        Mage_Sales_Model_Order::STATE_COMPLETE,
        Mage_Sales_Model_Order::STATE_CLOSED,
        Mage_Sales_Model_Order::STATE_CANCELED,
        Mage_Sales_Model_Order::STATE_HOLDED,
    ];

    public function toOptionArray()
    {
        if ($this->_stateStatuses) {
            $statuses = Mage::getSingleton('sales/order_config')->getStateStatuses($this->_stateStatuses);
        } else {
            $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        }
        $options = [];
        $options[] = [
            'value' => '',
            'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
        ];
        foreach ($statuses as $code => $label) {
            $options[] = [
                'value' => $code,
                'label' => $label,
            ];
        }
        return $options;
    }
}
