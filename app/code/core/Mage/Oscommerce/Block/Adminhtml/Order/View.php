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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce order view
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Block_Adminhtml_Order_View extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('oscommerce/order/view.phtml');


    }

    protected function _prepareLayout()
    {
        $this->setChild('backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'  => Mage::helper('oscommerce')->__('Back'),
                    'id'     => 'back_button',
                    'name'   => 'back_button',
                    'element_name' => 'back_button',
                    'class'  => 'scalable back',
                    'onclick'=> "setLocation('".$this->getBackUrl()."')",
                ))
         );
    }
    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_oscommerce_order');
    }

    /**
     * Retrieve Order Identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    public function getHeaderText()
    {
        $text = Mage::helper('oscommerce')->__('osCommerce Order # %s | Order Date %s',
            $this->getOrder()->getOrdersId(),
            $this->formatDate($this->getOrder()->getDatePurchased(), 'medium', true)
        );
        return $text;
    }

    public function getUrl($params='', $params2=array())
    {
        $params2['order_id'] = $this->getOrderId();
        return parent::getUrl($params, $params2);
    }


    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getBackUrl()
    {
        return $this->getUrl("*/*/");
    }
}
