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
 * Form image file element
 *
 * @category   Varien
 * @package    Varien_Data
 *
 * @method $this setAutosubmit(bool $false)
 */
class Varien_Data_Form_Element_Imagefile extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Imagefile constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('file');
        $this->setExtType('imagefile');
        $this->setAutosubmit(false);
        $this->setData('autoSubmit', false);
        //$this->setExtType('file');
    }
}
