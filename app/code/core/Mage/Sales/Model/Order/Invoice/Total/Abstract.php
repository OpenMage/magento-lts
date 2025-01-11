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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base class for invoice total
 *
 * @category   Mage
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Order_Invoice_Total_Abstract extends Mage_Sales_Model_Order_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @return Mage_Sales_Model_Order_Invoice_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        return $this;
    }
}
