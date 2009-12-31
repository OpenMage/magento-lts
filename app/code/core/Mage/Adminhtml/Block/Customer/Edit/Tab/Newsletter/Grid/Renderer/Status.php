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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter queue grid block status item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected static $_statuses;

    public function __construct()
    {
        self::$_statuses = array(
                Mage_Newsletter_Model_Queue::STATUS_SENT 	=> Mage::helper('customer')->__('Sent'),
                Mage_Newsletter_Model_Queue::STATUS_CANCEL	=> Mage::helper('customer')->__('Cancel'),
                Mage_Newsletter_Model_Queue::STATUS_NEVER 	=> Mage::helper('customer')->__('Not Sent'),
                Mage_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('customer')->__('Sending'),
                Mage_Newsletter_Model_Queue::STATUS_PAUSE 	=> Mage::helper('customer')->__('Paused'),
            );
        parent::__construct();
    }

    public function render(Varien_Object $row)
    {
        return Mage::helper('customer')->__($this->getStatus($row->getQueueStatus()));
    }

    public static function  getStatus($status)
    {
        if(isset(self::$_statuses[$status])) {
            return self::$_statuses[$status];
        }

        return Mage::helper('customer')->__('Unknown');
    }

}
