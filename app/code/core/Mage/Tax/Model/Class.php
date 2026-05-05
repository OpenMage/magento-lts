<?php

declare(strict_types=1);

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
 * @method Mage_Tax_Model_Resource_Class            _getResource()
 * @method Mage_Tax_Model_Resource_Class_Collection getCollection()
 * @method Mage_Tax_Model_Resource_Class            getResource()
 * @method Mage_Tax_Model_Resource_Class_Collection getResourceCollection()
 */
class Mage_Tax_Model_Class extends Mage_Core_Model_Abstract
{
    public const TAX_CLASS_TYPE_CUSTOMER   = 'CUSTOMER';

    public const TAX_CLASS_TYPE_PRODUCT    = 'PRODUCT';

    protected function _construct()
    {
        $this->_init('tax/class');
    }

    public function getClassName(): string
    {
        return (string) $this->_getData('class_name');
    }

    public function getClassType(): string
    {
        return (string) $this->_getData('class_type');
    }

    public function setClassName(string $value): static
    {
        return $this->setData('class_name', $value);
    }

    public function setClassType(string $value): static
    {
        return $this->setData('class_type', $value);
    }
}
