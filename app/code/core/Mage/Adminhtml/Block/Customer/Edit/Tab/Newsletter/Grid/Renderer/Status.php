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
 * Adminhtml newsletter queue grid block status item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $_statuses;

    public function __construct()
    {
        self::$_statuses = [
                Mage_Newsletter_Model_Queue::STATUS_SENT    => Mage::helper('customer')->__('Sent'),
                Mage_Newsletter_Model_Queue::STATUS_CANCEL  => Mage::helper('customer')->__('Cancel'),
                Mage_Newsletter_Model_Queue::STATUS_NEVER   => Mage::helper('customer')->__('Not Sent'),
                Mage_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('customer')->__('Sending'),
                Mage_Newsletter_Model_Queue::STATUS_PAUSE   => Mage::helper('customer')->__('Paused'),
        ];
        parent::__construct();
    }

    public function render(Varien_Object $row)
    {
        return Mage::helper('customer')->__(self::getStatus($row->getQueueStatus()));
    }

    public static function getStatus($status)
    {
        return self::$_statuses[$status] ?? Mage::helper('customer')->__('Unknown');
    }
}
