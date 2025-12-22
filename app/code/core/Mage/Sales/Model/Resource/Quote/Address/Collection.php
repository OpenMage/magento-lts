<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote addresses collection
 *
 * @package    Mage_Sales
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

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/quote_address');
    }

    /**
     * Setting filter on quote_id field but if quote_id is 0
     * we should exclude loading junk data from DB
     *
     * @param  int   $quoteId
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

        Mage::dispatchEvent($this->_eventPrefix . '_load_after', [
            $this->_eventObject => $this,
        ]);

        return $this;
    }
}
