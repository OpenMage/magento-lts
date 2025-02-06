<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Class Mage_Checkout_Block_Agreements
 *
 * @category   Mage
 * @package    Mage_Checkout
 *
 * @method bool hasAgreements()
 * @method $this setAgreements(Mage_Checkout_Model_Resource_Agreement_Collection $value)
 */
class Mage_Checkout_Block_Agreements extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getAgreements()
    {
        if (!$this->hasAgreements()) {
            if (!Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
                $agreements = [];
            } else {
                $agreements = Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1)
                    ->setOrder('position', Varien_Data_Collection::SORT_ORDER_ASC);
            }
            $this->setAgreements($agreements);
        }
        return $this->getData('agreements');
    }
}
