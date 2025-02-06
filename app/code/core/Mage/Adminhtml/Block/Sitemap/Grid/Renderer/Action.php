<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Sitemap grid action column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sitemap_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $this->getColumn()->setActions([[
            'url'     => $this->getUrl('*/sitemap/generate', ['sitemap_id' => $row->getSitemapId()]),
            'caption' => Mage::helper('sitemap')->__('Generate'),
        ]]);
        return parent::render($row);
    }
}
