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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search Order Model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Search_Order extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Mage_Adminhtml_Model_Search_Order
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $query = $this->getQuery();
        //TODO: add full name logic
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToSearchFilter(array(
                array('attribute' => 'increment_id',       'like'=>$query.'%'),
                array('attribute' => 'billing_firstname',  'like'=>$query.'%'),
                array('attribute' => 'billing_lastname',   'like'=>$query.'%'),
                array('attribute' => 'billing_telephone',  'like'=>$query.'%'),
                array('attribute' => 'billing_postcode',   'like'=>$query.'%'),

                array('attribute' => 'shipping_firstname', 'like'=>$query.'%'),
                array('attribute' => 'shipping_lastname',  'like'=>$query.'%'),
                array('attribute' => 'shipping_telephone', 'like'=>$query.'%'),
                array('attribute' => 'shipping_postcode',  'like'=>$query.'%'),
            ))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $order) {
            $arr[] = array(
                'id'                => 'order/1/'.$order->getId(),
                'type'              => Mage::helper('adminhtml')->__('Order'),
                'name'              => Mage::helper('adminhtml')->__('Order #%s', $order->getIncrementId()),
                'description'       => $order->getBillingFirstname().' '.$order->getBillingLastname(),
                'form_panel_title'  => Mage::helper('adminhtml')->__('Order #%s (%s)', $order->getIncrementId(), $order->getBillingFirstname().' '.$order->getBillingLastname()),
                'url' => Mage::helper('adminhtml')->getUrl('*/sales_order/view', array('order_id'=>$order->getId())),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
