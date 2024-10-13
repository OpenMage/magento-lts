<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Cms
 */
class Mage_Cms_Block_Adminhtml_Page_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Mage_Cms_Model_Page $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        Mage::dispatchEvent('adminhtml_cms_page_grid_renderer_action_before_render', ['row' => $row]);
        if ($row->getPreviewUrl()) {
            $href = $row->getPreviewUrl();
        } else {
            $urlModel = Mage::getModel('core/url')->setStore($row->getData('_first_store_id'));
            $href = $urlModel->getDirectUrl(
                $row->getIdentifier(),
                [
                    '_current' => false,
                    '_query'   => '___store=' . $row->getStoreCode(),
                ]
            );
        }
        return '<a href="' . $href . '" target="_blank">' . $this->__('Preview') . '</a>';
    }
}
