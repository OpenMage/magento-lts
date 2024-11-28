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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Info extends Varien_Data_Form_Element_Abstract
{
    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this
            ->setType('info')
            ->unsScope()
            ->unsCanUseDefaultValue()
            ->unsCanUseWebsiteValue();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $id = $this->getHtmlId();
        $label = $this->getLabel();
        return '<tr class="' . $id . '"><td class="label" colspan="99"><label>' . $label . '</label></td></tr>';
    }
}
