<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product alert for changed price collection
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Resource_Price_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define price collection
     *
     */
    protected function _construct()
    {
        $this->_init('productalert/price');
    }

    /**
     * Add customer filter
     *
     * @param mixed $customer
     * @return $this
     */
    public function addCustomerFilter($customer)
    {
        if (is_array($customer)) {
            $condition = $this->getConnection()->quoteInto('customer_id IN(?)', $customer);
        } elseif ($customer instanceof Mage_Customer_Model_Customer) {
            $condition = $this->getConnection()->quoteInto('customer_id=?', $customer->getId());
        } else {
            $condition = $this->getConnection()->quoteInto('customer_id=?', $customer);
        }
        $this->addFilter('customer_id', $condition, 'string');
        return $this;
    }

    /**
     * Add website filter
     *
     * @param mixed $website
     * @return $this
     */
    public function addWebsiteFilter($website)
    {
        if (is_null($website) || $website == 0) {
            return $this;
        }
        if (is_array($website)) {
            $condition = $this->getConnection()->quoteInto('website_id IN(?)', $website);
        } elseif ($website instanceof Mage_Core_Model_Website) {
            $condition = $this->getConnection()->quoteInto('website_id=?', $website->getId());
        } else {
            $condition = $this->getConnection()->quoteInto('website_id=?', $website);
        }
        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    /**
     * Set order by customer
     *
     * @param string $sort
     * @return $this
     */
    public function setCustomerOrder($sort = 'ASC')
    {
        $this->getSelect()->order('customer_id ' . $sort);
        return $this;
    }
}
