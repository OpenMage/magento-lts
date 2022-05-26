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
 * Adminhtml permissions block grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsBlockGrid');
        $this->setDefaultSort('block_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getResourceModel('admin/block_collection'));
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('block_id', array(
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => 5,
            'align'     => 'right',
            'sortable'  => true,
            'index'     => 'block_id'
        ));

        $this->addColumn('block_name', array(
            'header'    => Mage::helper('adminhtml')->__('Block Name'),
            'index'     => 'block_name'
        ));

        $this->addColumn('is_allowed', array(
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_allowed',
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('adminhtml')->__('Allowed'),
                '0' => Mage::helper('adminhtml')->__('Not allowed')
             ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('block_id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/blockGrid');
    }
}
