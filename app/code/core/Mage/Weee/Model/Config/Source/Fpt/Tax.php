<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Config_Source_Fpt_Tax
{
    /**
     * Array of options for FPT Tax Configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        $weeeHelper = $this->_getHelper('weee');
        return [
            ['value' => 0, 'label' => $weeeHelper->__('Not Taxed')],
            ['value' => 1, 'label' => $weeeHelper->__('Taxed')],
            ['value' => 2, 'label' => $weeeHelper->__('Loaded and Displayed with Tax')],
        ];
    }

    /**
     * Return helper corresponding to given name
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }
}
