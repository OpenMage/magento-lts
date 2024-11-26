<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable links API model
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Link_Api_V2 extends Mage_Downloadable_Model_Link_Api
{
    /**
     * Clean the object, leave only property values
     *
     * @param object $var
     * @param-out array $var
     */
    protected function _prepareData(&$var)
    {
        if (is_object($var)) {
            $var = get_object_vars($var);
            foreach ($var as $key => &$value) {
                $this->_prepareData($value);
            }
        }
    }

    /**
     * Add downloadable content to product
     *
     * @param int|string $productId
     * @param object $resource
     * @param string $resourceType
     * @param string|int $store
     * @param string $identifierType ('sku'|'id')
     * @return bool
     */
    public function add($productId, $resource, $resourceType, $store = null, $identifierType = null)
    {
        $this->_prepareData($resource);
        return parent::add($productId, $resource, $resourceType, $store, $identifierType);
    }
}
