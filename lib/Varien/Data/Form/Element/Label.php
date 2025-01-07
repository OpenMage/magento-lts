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
 * Data form abstract class
 *
 * @category   Varien
 * @package    Varien_Data
 *
 * @method bool getBold()
 */
class Varien_Data_Form_Element_Label extends Varien_Data_Form_Element_Abstract
{
    /**
     * Assigns attributes for Element
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    /**
     * Retrieve Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getBold() ? '<strong>' : '';
        $html .= $this->getEscapedValue();
        $html .= $this->getBold() ? '</strong>' : '';
        return $html . $this->getAfterElementHtml();
    }
}
