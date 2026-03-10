<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order abstract comments collection, used as parent for: invoice, shipment, creditmemo
 *
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Set filter on comments by their parent item
     *
     * @param  int|Mage_Core_Model_Abstract $parent
     * @return $this
     */
    public function setParentFilter($parent)
    {
        if ($parent instanceof Mage_Core_Model_Abstract) {
            $parent = $parent->getId();
        }

        return $this->addFieldToFilter('parent_id', $parent);
    }

    /**
     * Adds filter to get only 'visible on front' comments
     *
     * @param  int   $flag
     * @return $this
     */
    public function addVisibleOnFrontFilter($flag = 1)
    {
        return $this->addFieldToFilter('is_visible_on_front', $flag);
    }

    /**
     * Set created_at sort order
     *
     * @param  string $direction
     * @return $this
     */
    public function setCreatedAtOrder($direction = 'desc')
    {
        return $this->setOrder('created_at', $direction);
    }
}
