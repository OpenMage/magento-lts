<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax class model
 *
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Resource_Class _getResource()
 * @method string getClassName()
 * @method string getClassType()
 * @method Mage_Tax_Model_Resource_Class_Collection getCollection()
 * @method Mage_Tax_Model_Resource_Class getResource()
 * @method Mage_Tax_Model_Resource_Class_Collection getResourceCollection()
 * @method $this setClassName(string $value)
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
