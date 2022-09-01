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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        Mage::dispatchEvent('adminhtml_cms_page_grid_renderer_action_before_render', ['row' => $row]);
        if ($row->getPreviewUrl()) {
            $href = $row->getPreviewUrl();
        } else {
            $urlModel = Mage::getModel('core/url')->setStore($row->getData('_first_store_id'));
            $href = $urlModel->getUrl(
                $row->getIdentifier(), [
                    '_current' => false,
                    '_query'   => '___store=' . $row->getStoreCode(),
                ]
            );
        }
        return '<a href="' . $href . '" target="_blank">' . $this->__('Preview') . '</a>';
    }
}
