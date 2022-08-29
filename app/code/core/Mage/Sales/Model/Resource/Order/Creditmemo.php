<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order creditmemo resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Creditmemo extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                  = 'sales_order_creditmemo_resource';

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
    protected $_entityTypeForIncrementId     = 'creditmemo';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/creditmemo', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return $this
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter           = $this->getReadConnection();
        $checkedFirstname  = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $checkedMiddlename = $adapter->getIfNullSql('{{table}}.middlename', $adapter->quote(''));
        $checkedLastname   = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatName        = $adapter->getConcatSql([
            $checkedFirstname,
            $adapter->quote(' '),
            $checkedMiddlename,
            $adapter->quote(' '),
            $checkedLastname
        ]);
        $concatName = new Zend_Db_Expr("TRIM(REPLACE($concatName,'  ', ' '))");

        $this->addVirtualGridColumn(
            'billing_name',
            'sales/order_address',
            ['billing_address_id' => 'entity_id'],
            $concatName
        )
        ->addVirtualGridColumn(
            'order_increment_id',
            'sales/order',
            ['order_id' => 'entity_id'],
            'increment_id'
        )
        ->addVirtualGridColumn(
            'order_created_at',
            'sales/order',
            ['order_id' => 'entity_id'],
            'created_at'
        );

        return $this;
    }
}
