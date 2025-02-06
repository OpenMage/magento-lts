<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Tax class model
 *
 * @category   Mage
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Resource_Class _getResource()
 * @method Mage_Tax_Model_Resource_Class getResource()
 * @method Mage_Tax_Model_Resource_Class_Collection getCollection()
 *
 * @method string getClassName()
 * @method $this setClassName(string $value)
 * @method string getClassType()
 * @method $this setClassType(string $value)
 */
class Mage_Tax_Model_Class extends Mage_Core_Model_Abstract
{
    public const TAX_CLASS_TYPE_CUSTOMER   = 'CUSTOMER';
    public const TAX_CLASS_TYPE_PRODUCT    = 'PRODUCT';

    public function _construct()
    {
        $this->_init('tax/class');
    }
}
