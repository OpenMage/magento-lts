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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Products' tags grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Tag extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tag/tag')
            ->getResourceCollection()
            ->addProductFilter($this->getProductId())
            ->addPopularity();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Tag Name'),
            'index'     => 'name',
        ));

        $this->addColumn('popularity', array(
            'header'        => Mage::helper('catalog')->__('# of Use'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'popularity',
            'type'          => 'number',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('catalog')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('catalog')->__('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('catalog')->__('Approved'),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/tag/edit', array(
            'tag_id'        => $row->getId(),
            'product_id'    => $this->getProductId(),
        ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product/tagGrid', array(
            '_current'      => true,
            'id'            => $this->getProductId(),
            'product_id'    => $this->getProductId(),
        ));
    }
}
