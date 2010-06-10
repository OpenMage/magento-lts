<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cache management edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Cache_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/cache/edit.phtml');
        $this->setTitle('Cache Management');
    }

    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Save Cache Settings'),
                    'onclick'   => 'configForm.submit()',
                    'class' => 'save',
                ))
        );
        return parent::_prepareLayout();
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function initForm()
    {
        $this->setChild('form',
            $this->getLayout()->createBlock('adminhtml/system_cache_form')
                ->initForm()
        );
        return $this;
    }

    /**
     * Retrieve Catalog Tools Data
     *
     * @return array
     */
    public function getCatalogData()
    {
        $layeredIsDisabled = false;
        $warning = '';

        $flag = Mage::getModel('catalogindex/catalog_index_flag')->loadSelf();
        switch ($flag->getState()) {
            case Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED:
                $layeredAction = Mage::helper('adminhtml')->__('Queued... Cancel');
                //$layeredIsDisabled = true;
                break;
            case Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING:
                $layeredAction = Mage::helper('adminhtml')->__('Running... Kill');
                $warning = Mage::helper('adminhtml')->__('Do you really want to KILL parallel process and start new indexing process?');
                //$layeredIsDisabled = true;
                //$nowIsDisabled = true;
                break;
            default:
                $layeredAction = Mage::helper('adminhtml')->__('Queue Refresh');
                //$layeredIsDisabled = false;
                break;
        }

        return array(
            'refresh_catalog_rewrites'   => array(
                'label'     => Mage::helper('adminhtml')->__('Catalog Rewrites'),
                'buttons'   => array(
                    array(
                        'name'      => 'refresh_catalog_rewrites',
                        'action'    => Mage::helper('adminhtml')->__('Refresh'),
                        )
                ),
            ),
            'clear_images_cache'         => array(
                'label'     => Mage::helper('adminhtml')->__('Images Cache'),
                'buttons'   => array(
                    array(
                        'name'      => 'clear_images_cache',
                        'action'    => Mage::helper('adminhtml')->__('Clear'),
                        )
                ),
            ),
            'refresh_layered_navigation' => array(
                'label'     => Mage::helper('adminhtml')->__('Layered Navigation Indices'),
                'buttons'   => array(
                    array(
                        'name'      => 'refresh_layered_navigation',
                        'action'    => $layeredAction,
                        'disabled'  => $layeredIsDisabled,
                        ),
                    array(
                        'name'      => 'refresh_layered_navigation_now',
                        'action'    => Mage::helper('adminhtml')->__('Refresh Now*'),
                        'comment'   => Mage::helper('adminhtml')->__('* - If indexing is in progress, it will be killed and new indexing process will start.'),
                        'warning'   => $warning,
                        )
                ),
            ),
            'rebuild_search_index'      => array(
                'label'     => Mage::helper('adminhtml')->__('Search Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_search_index',
                        'action'    => Mage::helper('adminhtml')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_inventory_stock_status' => array(
                'label'     => Mage::helper('adminhtml')->__('Inventory Stock Status'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_inventory_stock_status',
                        'action'    => Mage::helper('adminhtml')->__('Refresh'),
                    )
                ),
            ),
            'rebuild_catalog_index'         => array(
                'label'     => Mage::helper('adminhtml')->__('Rebuild Catalog Index'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_catalog_index',
                        'action'    => Mage::helper('adminhtml')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_category' => array(
                'label'     => Mage::helper('adminhtml')->__('Rebuild Flat Catalog Category'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_category',
                        'action'    => Mage::helper('adminhtml')->__('Rebuild'),
                    )
                ),
            ),
            'rebuild_flat_catalog_product' => array(
                'label'     => Mage::helper('adminhtml')->__('Rebuild Flat Catalog Product'),
                'buttons'   => array(
                    array(
                        'name'      => 'rebuild_flat_catalog_product',
                        'action'    => Mage::helper('adminhtml')->__('Rebuild'),
                    )
                ),
            ),
        );
    }
}
