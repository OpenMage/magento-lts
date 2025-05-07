<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml permissions orphanedResource grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_OrphanedResource_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsOrphanedResourceGrid');
        $this->setDefaultSort('resource_id');
        $this->setDefaultDir('asc');
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('admin/rules_collection')
            ->addFieldToFilter('resource_id', ['nin' => Mage::getSingleton('admin/session')->getAcl()->getResources()])
            ->addFieldToSelect('resource_id');
        $collection->getSelect()->group('resource_id');

        /**
         * In order for mass action selection to work properly, we need to overwrite
         * the model resource $_idFieldName, from the default 'rule_id' to 'resource_id'.
         * @see Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract::getGridIdsJson()
         * @var Mage_Admin_Model_Resource_Rules $resource
         */
        $resource = $collection->getResource();
        $resource->setResourceIdAsIdFieldName();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('resource_id', [
            'header' => Mage::helper('adminhtml')->__('Orphaned Role Resource'),
            'index' => 'resource_id',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('resource_id');
        $this->getMassactionBlock()->setFormFieldName('resource_id');

        $this->getMassactionBlock()->addItem(MassAction::DELETE, [
            'label'    => Mage::helper('adminhtml')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
        ]);

        return $this;
    }

    public function getRowUrl($row): string
    {
        return '';
    }
}
