<?php
/**
 * Customer Form Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Form extends Mage_Eav_Model_Form
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
     * Get EAV Entity Form Attribute Collection for Customer
     * exclude 'created_at'
     *
     * @return Mage_Customer_Model_Resource_Form_Attribute_Collection
     */
    protected function _getFormAttributeCollection()
    {
        return parent::_getFormAttributeCollection()
            ->addFieldToFilter('attribute_code', ['neq' => 'created_at']);
    }
}
