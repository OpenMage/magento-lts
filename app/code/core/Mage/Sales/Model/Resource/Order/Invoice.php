<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order invoice resource
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix                  = 'sales_order_invoice_resource';

    /**
     * Is grid available
     *
     * @var bool
     */
    protected $_grid                         = true;

    /**
     * Flag for using of increment id
     *
     * @var bool
     */
    protected $_useIncrementId               = true;

    /**
     * Entity code for increment id (Eav entity code)
     *
     * @var string
     */
    protected $_entityTypeForIncrementId     = 'invoice';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/invoice', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return $this
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter           = $this->_getReadAdapter();
        $checkedFirstname  = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $checkedMiddlename = $adapter->getIfNullSql('{{table}}.middlename', $adapter->quote(''));
        $checkedLastname   = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatName = $adapter->getConcatSql([
            $checkedFirstname,
            $adapter->quote(' '),
            $checkedMiddlename,
            $adapter->quote(' '),
            $checkedLastname,
        ]);
        $concatName = new Zend_Db_Expr("TRIM(REPLACE($concatName,'  ', ' '))");

        $this->addVirtualGridColumn(
            'billing_name',
            'sales/order_address',
            ['billing_address_id' => 'entity_id'],
            $concatName,
        )
        ->addVirtualGridColumn(
            'order_increment_id',
            'sales/order',
            ['order_id' => 'entity_id'],
            'increment_id',
        )
        ->addVirtualGridColumn(
            'order_created_at',
            'sales/order',
            ['order_id' => 'entity_id'],
            'created_at',
        );

        return $this;
    }
}
