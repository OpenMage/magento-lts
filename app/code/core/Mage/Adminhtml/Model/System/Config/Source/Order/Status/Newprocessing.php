<?php
/**
 * Order Statuses source model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Order_Status_Newprocessing extends Mage_Adminhtml_Model_System_Config_Source_Order_Status
{
    protected $_stateStatuses = [
        Mage_Sales_Model_Order::STATE_NEW,
        Mage_Sales_Model_Order::STATE_PROCESSING,
    ];
}
