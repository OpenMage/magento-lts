<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sitemaps grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sitemap_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sitemapGrid');
        $this->setDefaultSort('sitemap_id');
    }

    protected function _prepareCollection()
    {
        /** @var Mage_Sitemap_Model_Resource_Sitemap_Collection $collection */
        $collection = Mage::getModel('sitemap/sitemap')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('sitemap_id', [
            'header'    => Mage::helper('sitemap')->__('ID'),
            'width'     => '50px',
            'index'     => 'sitemap_id'
        ]);

        $this->addColumn('sitemap_filename', [
            'header'    => Mage::helper('sitemap')->__('Filename'),
            'index'     => 'sitemap_filename'
        ]);

        $this->addColumn('sitemap_path', [
            'header'    => Mage::helper('sitemap')->__('Path'),
            'index'     => 'sitemap_path'
        ]);

        $this->addColumn('link', [
            'header'    => Mage::helper('sitemap')->__('Link for Google'),
            'index'     => 'concat(sitemap_path, sitemap_filename)',
            'renderer'  => 'adminhtml/sitemap_grid_renderer_link',
        ]);

        $this->addColumn('sitemap_time', [
            'header'    => Mage::helper('sitemap')->__('Last Time Generated'),
            'index'     => 'sitemap_time',
            'type'      => 'datetime',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'    => Mage::helper('sitemap')->__('Store View'),
                'index'     => 'store_id',
                'type'      => 'store',
            ]);
        }

        $this->addColumn('action', [
            'header'   => Mage::helper('sitemap')->__('Action'),
            'filter'   => false,
            'sortable' => false,
            'width'    => '100',
            'renderer' => 'adminhtml/sitemap_grid_renderer_action'
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['sitemap_id' => $row->getId()]);
    }
}
