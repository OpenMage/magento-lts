<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote addresses collection
 *
 * @category   Mage
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
