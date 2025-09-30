<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/**
 * @package    Mage_CatalogRule
 */
class Mage_CatalogRule_Model_Rule_Action_Collection extends Mage_Rule_Model_Action_Collection
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('catalogrule/rule_action_collection');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        return array_merge_recursive($actions, [
            ['value' => 'catalogrule/rule_action_product', 'label' => Mage::helper('cataloginventory')->__('Update the Product')],
        ]);
    }
}
