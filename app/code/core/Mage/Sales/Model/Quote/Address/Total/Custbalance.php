<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Address_Total_Custbalance extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setCustbalanceAmount(0);
        $address->setBaseCustbalanceAmount(0);

        $address->setGrandTotal($address->getGrandTotal() - $address->getCustbalanceAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $address->getBaseCustbalanceAmount());

        return $this;
    }
}
