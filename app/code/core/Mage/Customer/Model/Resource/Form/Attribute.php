<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customer Form Attribute Resource Model
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Form_Attribute extends Mage_Eav_Model_Resource_Form_Attribute
{
    protected function _construct()
    {
        $this->_init('customer/form_attribute', 'attribute_id');
    }
}
