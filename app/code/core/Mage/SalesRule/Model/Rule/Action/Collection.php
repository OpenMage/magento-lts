<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/**
 * Class Mage_SalesRule_Model_Rule_Action_Collection
 *
 * @category   Mage
 * @package    Mage_SalesRule
 *
 * @method $this setType(string $value)
 */
class Mage_SalesRule_Model_Rule_Action_Collection extends Mage_Rule_Model_Action_Collection
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_action_collection');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        return array_merge_recursive($actions, [
            ['value' => 'salesrule/rule_action_product', 'label' => Mage::helper('salesrule')->__('Update the Product')],
        ]);
    }
}
