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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search Order Model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method bool hasLimit()
 * @method int getLimit()
 * @method bool hasQuery()
 * @method string getQuery()
 * @method bool setResults(array $value)
 * @method bool hasStart()
 * @method int getStart()
 */
class Mage_Adminhtml_Model_Search_Order extends Varien_Object
{
    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $arr = [];

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }

        $query = $this->getQuery();
        //TODO: add full name logic
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToSearchFilter([
                ['attribute' => 'increment_id',       'like' => $query . '%'],
                ['attribute' => 'billing_firstname',  'like' => $query . '%'],
                ['attribute' => 'billing_lastname',   'like' => $query . '%'],
                ['attribute' => 'billing_telephone',  'like' => $query . '%'],
                ['attribute' => 'billing_postcode',   'like' => $query . '%'],

                ['attribute' => 'shipping_firstname', 'like' => $query . '%'],
                ['attribute' => 'shipping_lastname',  'like' => $query . '%'],
                ['attribute' => 'shipping_telephone', 'like' => $query . '%'],
                ['attribute' => 'shipping_postcode',  'like' => $query . '%'],
            ])
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $order) {
            $arr[] = [
                'id'                => 'order/1/' . $order->getId(),
                'type'              => Mage::helper('adminhtml')->__('Order'),
                'name'              => Mage::helper('adminhtml')->__('Order #%s', $order->getIncrementId()),
                'description'       => $order->getBillingFirstname() . ' ' . $order->getBillingLastname(),
                'form_panel_title'  => Mage::helper('adminhtml')->__('Order #%s (%s)', $order->getIncrementId(), $order->getBillingFirstname() . ' ' . $order->getBillingLastname()),
                'url' => Mage::helper('adminhtml')->getUrl('*/sales_order/view', ['order_id' => $order->getId()]),
            ];
        }

        $this->setResults($arr);

        return $this;
    }
}
