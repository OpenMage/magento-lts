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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote addresses collection
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_quote_address_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'quote_address_collection';

    protected function _construct()
    {
        $this->_init('sales/quote_address');
    }

    /**
     * Setting filter on quote_id field but if quote_id is 0
     * we should exclude loading junk data from DB
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteFilter($quoteId)
    {
        $this->addFieldToFilter('quote_id', $quoteId ? $quoteId : ['null' => 1]);
        return $this;
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        Mage::dispatchEvent($this->_eventPrefix.'_load_after', [
            $this->_eventObject => $this
        ]);

        return $this;
    }
}
