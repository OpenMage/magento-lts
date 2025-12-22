<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Class Mage_Checkout_Block_Agreements
 *
 * @package    Mage_Checkout
 *
 * @method bool  hasAgreements()
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
