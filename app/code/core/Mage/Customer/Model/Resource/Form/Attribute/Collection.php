<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer Form Attribute Resource Collection
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Form_Attribute_Collection extends Mage_Eav_Model_Resource_Form_Attribute_Collection
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'customer';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'customer';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('eav/attribute', 'customer/form_attribute');
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored.
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return null|string
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('customer/eav_attribute_website');
    }
}
