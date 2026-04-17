<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Shopingcart Products Report collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Shopcart_Product_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * Join fields
     *
     * @return $this
     */
    protected function _joinFields()
    {
        parent::_joinFields();
        $this->addAttributeToSelect('price')
            ->addCartsCount()
            ->addOrdersCount();

        return $this;
    }

    /**
     * Set date range
     *
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    public function setDateRange($dateFrom, $dateTo)
    {
        $this->getSelect()->reset();
        return $this;
    }
}
