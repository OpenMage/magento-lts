<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * Class Mage_Eav_Block_Widget_Abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 *
 * @method Mage_Core_Model_Abstract getAttribute()
 * @method $this setAttribute(Mage_Eav_Model_Entity_Attribute $value)
 * @method Mage_Core_Model_Abstract getObject()
 * @method $this setObject(Mage_Core_Model_Abstract $value)
 */
class Mage_Eav_Block_Widget_Abstract extends Mage_Core_Block_Template
{
    /**
     * Check if attribute enabled in system
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->getAttribute()->getIsVisible();
    }

    /**
     * Check if attribute marked as required
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return (bool)$this->getAttribute()->getIsRequired();
    }

    /**
     * @return string
     */
    public function getFieldIdFormat(): string
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    /**
     * @return string
     */
    public function getFieldNameFormat(): string
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    public function getFieldId(string $field): string
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    public function getFieldName(string $field): string
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }
}
