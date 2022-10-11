<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store render group
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 * @deprecated after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree
 */
class Mage_Adminhtml_Block_System_Store_Grid_Render_Group
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if (!$row->getData($this->getColumn()->getIndex())) {
            return null;
        }
        return '<a title="' . Mage::helper('core')->__('Edit Store') . '"
            href="' . $this->getUrl('*/*/editGroup', ['group_id' => $row->getGroupId()]) . '">'
            . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }
}
