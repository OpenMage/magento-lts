<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rule
 */

/**
 * Abstract Rule entity resource model
 *
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
