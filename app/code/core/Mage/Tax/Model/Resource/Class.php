<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax class resource
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Class extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('tax/tax_class', 'class_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [[
            'field' => ['class_type', 'class_name'],
            'title' => Mage::helper('tax')->__('An error occurred while saving this tax class. A class with the same name'),
        ]];
        return $this;
    }
}
