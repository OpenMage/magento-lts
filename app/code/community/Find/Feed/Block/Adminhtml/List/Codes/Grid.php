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
 * @category    
 * @package     _home
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * TheFind feed attribute map Grid
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_List_Codes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid settings
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('find_feed_list_codes');
        $this->setUseAjax(true);
    }

    /**
     * Prepare codes collection
     *
     * @return Find_Feed_Block_Adminhtml_List_Codes_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('find_feed/codes_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('import_code', array(
            'header'=> Mage::helper('find_feed')->__('Feed code'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'import_code'
        ));

        $this->addColumn('eav_code', array(
            'header'=> Mage::helper('find_feed')->__('Eav code'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'eav_code'
        ));

        $source = Mage::getModel('eav/entity_attribute_source_boolean');
        $isImportedOptions = $source->getOptionArray();

        $this->addColumn('is_imported', array(
            'header' => Mage::helper('find_feed')->__('In feed'),
            'width' => '100px',
            'index' => 'is_imported',
            'type'  => 'options',
            'options' => $isImportedOptions
        ));
        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('code_id');
        $this->getMassactionBlock()->setFormFieldName('code_id');

        $this->getMassactionBlock()->addItem('enable', array(
            'label'         => Mage::helper('find_feed')->__('Import'),
            'url'           => $this->getUrl('*/codes_grid/massEnable'),
            'selected'      => true,
        ));
        $this->getMassactionBlock()->addItem('disable', array(
            'label'         => Mage::helper('find_feed')->__('Not import'),
            'url'           => $this->getUrl('*/codes_grid/massDisable'),
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label'         => Mage::helper('find_feed')->__('Delete'),
            'url'           => $this->getUrl('*/codes_grid/delete'),
        ));

        return $this;
    }

    /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/codes_grid/grid', array('_current'=>true));
    }
}
