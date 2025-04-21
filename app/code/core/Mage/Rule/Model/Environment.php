<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Rule_Model_Environment
 *
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
