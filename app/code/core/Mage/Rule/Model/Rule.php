<?php
/**
 * Abstract Rule entity data model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Rule
 * @deprecated since 1.7.0.0 use Mage_Rule_Model_Abstract instead
 */
class Mage_Rule_Model_Rule extends Mage_Rule_Model_Abstract
{
    /**
     * Getter for rule combine conditions instance
     *
     * @return Mage_Rule_Model_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('rule/condition_combine');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Mage_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('rule/action_collection');
    }
}
