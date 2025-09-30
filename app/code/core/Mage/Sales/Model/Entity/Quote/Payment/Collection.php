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
class Mage_Sales_Model_Entity_Quote_Payment_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_payment');
    }

    /**
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteFilter($quoteId)
    {
        $this->addAttributeToFilter('parent_id', $quoteId);
        return $this;
    }
}
