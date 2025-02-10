<?php
/**
 * Adminhtml catalog product downloadable items tab and form
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Reference to product objects that is being edited
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    protected $_config = null;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadable/product/edit/downloadable.phtml');
    }

    /**
     * Check is readonly block
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getDownloadableReadonly();
    }

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('downloadable')->__('Downloadable Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('downloadable')->__('Downloadable Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Render block HTML
     *
     * @inheritDoc
     */
    protected function _toHtml()
    {
        /** @var Mage_Adminhtml_Block_Widget_Accordion $accordion */
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion');
        $accordion->setId('downloadableInfo');

        $accordion->addItem('samples', [
            'title'   => Mage::helper('adminhtml')->__('Samples'),
            'content' => $this->getLayout()
                ->createBlock('downloadable/adminhtml_catalog_product_edit_tab_downloadable_samples')->toHtml(),
            'open'    => false,
        ]);

        $accordion->addItem('links', [
            'title'   => Mage::helper('adminhtml')->__('Links'),
            'content' => $this->getLayout()->createBlock(
                'downloadable/adminhtml_catalog_product_edit_tab_downloadable_links',
                'catalog.product.edit.tab.downloadable.links',
            )->toHtml(),
            'open'    => true,
        ]);

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }
}
