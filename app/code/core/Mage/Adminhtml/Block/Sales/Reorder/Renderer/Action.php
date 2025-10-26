<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml alert queue grid block action item renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Reorder_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Array to store all options data
     *
     * @var array
     */
    protected $_actions = [];

    public function render(Varien_Object $row)
    {
        $this->_actions = [];
        if (Mage::helper('sales/reorder')->canReorder($row)) {
            $reorderAction = [
                '@' => ['href' => $this->getUrl('*/sales_order_create/reorder', ['order_id' => $row->getId()])],
                '#' =>  Mage::helper('sales')->__('Reorder'),
            ];
            $this->addToActions($reorderAction);
        }

        Mage::dispatchEvent('adminhtml_customer_orders_add_action_renderer', ['renderer' => $this, 'row' => $row]);
        return $this->_actionsToHtml();
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value), '\\\'');
    }

    /**
     * Render options array as a HTML string
     *
     * @return string
     */
    protected function _actionsToHtml(array $actions = [])
    {
        $html = [];
        $attributesObject = new Varien_Object();

        if (empty($actions)) {
            $actions = $this->_actions;
        }

        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }

        return  implode('<span class="separator">|</span>', $html);
    }

    /**
     * Add one action array to all options data storage
     *
     * @param array $actionArray
     */
    public function addToActions($actionArray)
    {
        $this->_actions[] = $actionArray;
    }
}
