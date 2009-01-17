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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Model_Search_Order extends Varien_Object
{

    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        //TODO: add full name logic
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')

            ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id')
            ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id')
            ->joinAttribute('billing_telephone', 'order_address/telephone', 'billing_address_id')
            ->joinAttribute('billing_postcode', 'order_address/postcode', 'billing_address_id')

            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id')
            ->joinAttribute('shipping_telephone', 'order_address/telephone', 'shipping_address_id')
            ->joinAttribute('shipping_postcode', 'order_address/postcode', 'shipping_address_id')

            ->addAttributeToFilter(array(
                array('attribute'=>'billing_firstname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'billing_lastname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'billing_telephone', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'billing_postcode', 'like'=>$this->getQuery().'%'),

                array('attribute'=>'shipping_firstname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'shipping_lastname', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'shipping_telephone', 'like'=>$this->getQuery().'%'),
                array('attribute'=>'shipping_postcode', 'like'=>$this->getQuery().'%'),
            ))

            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $order) {
            $arr[] = array(
                'id'            => 'order/1/'.$order->getId(),
                'type'          => 'Order',
                'name'          => Mage::helper('adminhtml')->__('Order #%s', $order->getIncrementId()),
                'description'   => $order->getBillingFirstname().' '.$order->getBillingLastname(),
                'form_panel_title' => Mage::helper('adminhtml')->__('Order #%s (%s)', $order->getIncrementId(), $order->getBillingFirstname().' '.$order->getBillingLastname()),
                'url'           => Mage::helper('adminhtml')->getUrl('*/sales_order/view', array('order_id'=>$order->getId())),
            );
        }

        $this->setResults($arr);

        return $this;
    }

}

