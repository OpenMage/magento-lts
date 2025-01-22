<?php

/**
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax discount totals calculation model
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Sales_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Calculate discount tac amount
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
