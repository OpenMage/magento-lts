<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Quote entity resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Quote_Address extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('quote_address')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write'),
        );
    }

    /**
     * @return $this
     */
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $attributes = $this->loadAllAttributes()->getAttributesByCode();
        foreach ($attributes as $attrCode => $attr) {
            $backend = $attr->getBackend();
            if (method_exists($backend, 'collectTotals')) {
                $backend->collectTotals($address);
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $attributes = $this->loadAllAttributes()->getAttributesByCode();
        foreach ($attributes as $attrCode => $attr) {
            $frontend = $attr->getFrontend();
            if (method_exists($frontend, 'fetchTotals')) {
                $frontend->fetchTotals($address);
            }
        }

        return $this;
    }
}
