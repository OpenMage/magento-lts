<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Rule_Model_Environment
 *
 * @category   Mage
 * @package    Mage_Rule
 *
 * @method $this setNow(int $value)
 */
class Mage_Rule_Model_Environment extends Varien_Object
{
    /**
     * Collect application environment for rules filtering
     *
     * @todo make it not dependent on checkout module
     * @return $this
     */
    public function collect()
    {
        $this->setNow(time());

        Mage::dispatchEvent('rule_environment_collect', ['env' => $this]);

        return $this;
    }
}
