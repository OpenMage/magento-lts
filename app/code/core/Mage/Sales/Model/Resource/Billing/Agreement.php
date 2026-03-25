<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Billing agreement resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Billing_Agreement extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/billing_agreement', 'agreement_id');
    }

    /**
     * Add order relation to billing agreement
     *
     * @param  int   $agreementId
     * @param  int   $orderId
     * @return $this
     */
    public function addOrderRelation($agreementId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('sales/billing_agreement_order'),
            [
                'agreement_id'  => $agreementId,
                'order_id'      => $orderId,
            ],
        );
        return $this;
    }
}
