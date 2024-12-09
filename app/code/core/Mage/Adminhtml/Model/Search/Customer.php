<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search Customer Model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method bool hasLimit()
 * @method bool hasQuery()
 * @method string getQuery()
 * @method bool setResults(array $value)
 * @method bool hasStart()
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
