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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config element model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Config_Element extends Varien_Simplexml_Element
{
    /**
     * @param string $var
     * @param string|true $value
     * @return bool
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function is($var, $value = true)
    {
        $flag = $this->$var;

        if ($value === true) {
            $flag = strtolower((string)$flag);
            if (!empty($flag) && $flag !== 'false' && $flag !== 'off') {
                return true;
            } else {
                return false;
            }
        }

        return !empty($flag) && (strcasecmp((string)$value, (string)$flag) === 0);
    }

    /**
     * @return false|string
     */
    public function getClassName()
    {
        if ($this->class) {
            $model = (string)$this->class;
        } elseif ($this->model) {
            $model = (string)$this->model;
        } else {
            return false;
        }
        return Mage::getConfig()->getModelClassName($model);
    }
}
