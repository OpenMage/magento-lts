<?php

/**
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer api V2
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Api_V2 extends Mage_Customer_Model_Customer_Api
{
    /**
     * Prepare data to insert/update.
     * Creating array for stdClass Object
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareData($data)
    {
        if (($objectVars = get_object_vars($data)) !== null) {
            return parent::_prepareData($objectVars);
        }
        return [];
    }
}
