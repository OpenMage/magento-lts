<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Search Customer Model
 *
 * @package    Mage_Adminhtml
 *
 * @method string getQuery()
 * @method bool   hasLimit()
 * @method bool   hasQuery()
 * @method bool   hasStart()
 * @method bool   setResults(array $value)
 */
class Mage_Adminhtml_Model_Search_Customer extends Varien_Object
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

        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
            ->addAttributeToFilter([
                ['attribute' => 'firstname', 'like' => $this->getQuery() . '%'],
                ['attribute' => 'lastname', 'like'  => $this->getQuery() . '%'],
                ['attribute' => 'company', 'like'   => $this->getQuery() . '%'],
            ])
            ->setPage(1, 10)
            ->load();

        foreach ($collection->getItems() as $customer) {
            $arr[] = [
                'id'            => 'customer/1/' . $customer->getId(),
                'type'          => Mage::helper('adminhtml')->__('Customer'),
                'name'          => $customer->getName(),
                'description'   => $customer->getCompany(),
                'url' => Mage::helper('adminhtml')->getUrl('*/customer/edit', ['id' => $customer->getId()]),
            ];
        }

        $this->setResults($arr);

        return $this;
    }
}
