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
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product downloadable items tab and form
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @return boolean
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
                'catalog.product.edit.tab.downloadable.links'
            )->toHtml(),
            'open'    => true,
        ]);

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }
}
