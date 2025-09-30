<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer Form Attribute Resource Model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Form_Attribute extends Mage_Eav_Model_Resource_Form_Attribute
{
    protected function _construct()
    {
        $this->_init('customer/form_attribute', 'attribute_id');
    }
}
