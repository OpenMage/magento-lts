<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Flat sales order invoice grid collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection extends Mage_Sales_Model_Resource_Order_Invoice_Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_grid_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_invoice_grid_collection';

    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales/invoice_grid');
    }
}
