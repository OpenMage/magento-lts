<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form checkbox element
 *
 * @category   Varien
 * @package    Varien_Data
 *
 * @method bool getChecked()
 */
class Varien_Data_Form_Element_Checkbox extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Checkbox constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'title', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled', 'tabindex'];
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        if ($checked = $this->getChecked()) {
            $this->setData('checked', true);
        } else {
            $this->unsetData('checked');
        }
        return parent::getElementHtml();
    }

    /**
     * Set check status of checkbox
     *
     * @param boolean $value
     * @return Varien_Data_Form_Element_Checkbox
     */
    public function setIsChecked($value = false)
    {
        $this->setData('checked', $value);
        return $this;
    }

    /**
     * Return check status of checkbox
     *
     * @return boolean
     */
    public function getIsChecked()
    {
        return $this->getData('checked');
    }
}
