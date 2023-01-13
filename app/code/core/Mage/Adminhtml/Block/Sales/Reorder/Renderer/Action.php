<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml alert queue grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
                '#' =>  Mage::helper('sales')->__('Reorder')
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
     * @param array $actions
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
