<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Sitemaps grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
            'index'     => 'sitemap_id',
        ]);

        $this->addColumn('sitemap_filename', [
            'header'    => Mage::helper('sitemap')->__('Filename'),
            'index'     => 'sitemap_filename',
        ]);

        $this->addColumn('sitemap_path', [
            'header'    => Mage::helper('sitemap')->__('Path'),
            'index'     => 'sitemap_path',
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
                'type'      => 'store',
            ]);
        }

        $this->addColumn('action', [
            'type'     => 'action',
            'width'    => '100',
            'renderer' => 'adminhtml/sitemap_grid_renderer_action',
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
