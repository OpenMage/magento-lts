<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core unserialize helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_UnserializeArray
{
    /**
     * @param string $str
     * @return array
     * @throws Exception
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function unserialize($str)
    {
        try {
            $str = is_null($str) ? '' : $str;
            $result = @unserialize($str, ['allowed_classes' => false]);
            if ($result === false && $str !== serialize(false)) {
                throw new Exception('Error unserializing data.');
            }
            return $result;
        } catch (Error $e) {
            throw new Exception('Error unserializing data: ' . $e->getMessage(), 0, $e);
        }
    }
}
