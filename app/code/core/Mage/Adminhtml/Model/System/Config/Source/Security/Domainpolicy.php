<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Security_Domainpolicy
{
    /**
     * @var Mage_Adminhtml_Helper_Data
     */
    protected $_helper;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->_helper = $options['helper'] ?? Mage::helper('adminhtml');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Core_Model_Domainpolicy::FRAME_POLICY_ALLOW,
                'label' => $this->_helper->__('Enabled'),
            ],
            [
                'value' => Mage_Core_Model_Domainpolicy::FRAME_POLICY_ORIGIN,
                'label' => $this->_helper->__('Only from same domain'),
            ],
        ];
    }
}
