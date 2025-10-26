<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * HTML select element block with customer groups options
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup extends Mage_Core_Block_Html_Select
{
    /**
     * Customer groups cache
     *
     * @var array|null
     */
    private $_customerGroups;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addGroupAllOption = true;

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId  return name by customer group id
     * @return array|string
     */
    protected function _getCustomerGroups($groupId = null)
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = [];
            $collection = Mage::getModel('customer/group')->getCollection();
            foreach ($collection as $item) {
                /** @var Mage_Customer_Model_Group $item */
                $this->_customerGroups[$item->getId()] = $item->getCustomerGroupCode();
            }
        }

        if (!is_null($groupId)) {
            return $this->_customerGroups[$groupId] ?? null;
        }

        return $this->_customerGroups;
    }

    /**
     * @param string $value
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            if ($this->_addGroupAllOption) {
                $this->addOption(Mage_Customer_Model_Group::CUST_GROUP_ALL, Mage::helper('customer')->__('ALL GROUPS'));
            }

            foreach ($this->_getCustomerGroups() as $groupId => $groupLabel) {
                $this->addOption($groupId, addslashes($groupLabel));
            }
        }

        return parent::_toHtml();
    }
}
