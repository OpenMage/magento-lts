<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
