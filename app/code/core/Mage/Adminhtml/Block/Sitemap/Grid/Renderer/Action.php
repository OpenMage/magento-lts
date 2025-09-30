<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Sitemap grid action column renderer
 *
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
