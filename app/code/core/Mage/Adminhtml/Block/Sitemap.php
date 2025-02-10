<?php
/**
 * Adminhtml catalog (google) sitemaps block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sitemap extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_controller = 'sitemap';
        $this->_headerText = Mage::helper('sitemap')->__('Google Sitemap');
        $this->_addButtonLabel = Mage::helper('sitemap')->__('Add Sitemap');
        parent::__construct();
    }
}
