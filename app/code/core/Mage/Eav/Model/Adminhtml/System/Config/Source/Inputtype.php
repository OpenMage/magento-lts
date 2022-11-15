<?php

/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'text', 'label' => Mage::helper('eav')->__('Text Field')],
            ['value' => 'textarea', 'label' => Mage::helper('eav')->__('Text Area')],
            ['value' => 'date', 'label' => Mage::helper('eav')->__('Date')],
            ['value' => 'boolean', 'label' => Mage::helper('eav')->__('Yes/No')],
            ['value' => 'multiselect', 'label' => Mage::helper('eav')->__('Multiple Select')],
            ['value' => 'select', 'label' => Mage::helper('eav')->__('Dropdown')]
        ];
    }
}
