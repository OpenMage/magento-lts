<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert php serialize parser
 *
 * @category   Varien
 * @package    Varien_Convert
 */
class Varien_Convert_Parser_Serialize extends Varien_Convert_Parser_Abstract
{
    public function parse()
    {
        $this->setData(unserialize($this->getData()));
        return $this;
    }

    public function unparse()
    {
        $this->setData(serialize($this->getData()));
        return $this;
    }
}
