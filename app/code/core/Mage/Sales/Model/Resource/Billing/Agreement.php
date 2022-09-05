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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Billing agreement resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Billing_Agreement extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/billing_agreement', 'agreement_id');
    }

    /**
     * Add order relation to billing agreement
     *
     * @param int $agreementId
     * @param int $orderId
     * @return $this
     */
    public function addOrderRelation($agreementId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('sales/billing_agreement_order'),
            [
                'agreement_id'  => $agreementId,
                'order_id'      => $orderId
            ]
        );
        return $this;
    }
}
