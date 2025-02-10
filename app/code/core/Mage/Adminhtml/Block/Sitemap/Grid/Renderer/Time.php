<?php
/**
 * Sitemap grid link column renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sitemap_Grid_Renderer_Time extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Prepare link to display in grid
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        return date(
            Varien_Date::DATETIME_PHP_FORMAT,
            strtotime($row->getSitemapTime()) + Mage::getSingleton('core/date')->getGmtOffset(),
        );
    }
}
