<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote entity resource model
 *
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
