<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Search Order Model
 *
 * @package    Mage_Adminhtml
 *
 * @method int    getLimit()
 * @method string getQuery()
 * @method int    getStart()
 * @method bool   hasLimit()
 * @method bool   hasQuery()
 * @method bool   hasStart()
 * @method bool   setResults(array $value)
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

                ['attribute' => 'shipping_firstname', 'like' => $query . '%'],
                ['attribute' => 'shipping_lastname',  'like' => $query . '%'],
                ['attribute' => 'shipping_telephone', 'like' => $query . '%'],
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
