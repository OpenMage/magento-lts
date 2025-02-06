<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rule
 */

/**
 * Abstract Rule entity resource model
 *
 * @category   Mage
 * @package    Mage_Rule
 * @deprecated since 1.7.0.0 use Mage_Rule_Model_Resource_Abstract instead
 */
class Mage_Rule_Model_Resource_Rule extends Mage_Rule_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('rule/rule', 'rule_id');
    }
}
