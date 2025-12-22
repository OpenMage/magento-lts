<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer attribute resource model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Attribute extends Mage_Eav_Model_Resource_Attribute
{
    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return null|string
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('customer/eav_attribute_website');
    }

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids is stored
     *
     * @return null|string
     */
    protected function _getFormAttributeTable()
    {
        return $this->getTable('customer/form_attribute');
    }
}
