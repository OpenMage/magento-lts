<?php
/**
 * Block for Urlrewrites grid container
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Urlrewrite extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Part for generating appropriate grid block name
     *
     * @var string
     */
    protected $_controller = 'urlrewrite';

    /**
     * Set custom labels and headers
     *
     */
    public function __construct()
    {
        $this->_headerText = Mage::helper('adminhtml')->__('URL Rewrite Management');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add URL Rewrite');
        parent::__construct();
    }

    /**
     * Customize grid row URLs
     *
     * @see Mage_Adminhtml_Block_Urlrewrite_Selector
     * @return string
     */
    public function getCreateUrl()
    {
        $modes = array_keys(Mage::getBlockSingleton('adminhtml/urlrewrite_selector')->getModes());
        return $this->getUrl('*/*/edit') . array_shift($modes);
    }
}
