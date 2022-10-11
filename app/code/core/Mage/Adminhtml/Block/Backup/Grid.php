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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml backups grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('backupsGrid');
        $this->setDefaultSort('time');
        $this->setDefaultDir('desc');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('backup/fs_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare mass action controls
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()->addItem('delete', [
             'label'=> Mage::helper('adminhtml')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('backup')->__('Are you sure you want to delete the selected backup(s)?')
        ]);

        return $this;
    }

    /**
     * Configuration of grid
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $url7zip = Mage::helper('adminhtml')->__('The archive can be uncompressed with <a href="%s">%s</a> on Windows systems', 'http://www.7-zip.org/', '7-Zip');

        $this->addColumn('time', [
            'header'    => Mage::helper('backup')->__('Time'),
            'index'     => 'date_object',
            'type'      => 'datetime',
            'width'     => 200
        ]);

        $this->addColumn('display_name', [
            'header'    => Mage::helper('backup')->__('Name'),
            'index'     => 'display_name',
            'filter'    => false,
            'sortable'  => true,
            'width'     => 350
        ]);

        $this->addColumn('size', [
            'header'    => Mage::helper('backup')->__('Size, Bytes'),
            'index'     => 'size',
            'type'      => 'number',
            'sortable'  => true,
            'filter'    => false
        ]);

        $this->addColumn('type', [
            'header'    => Mage::helper('backup')->__('Type'),
            'type'      => 'options',
            'options'   => Mage::helper('backup')->getBackupTypes(),
            'index'     => 'type',
            'width'     => 300
        ]);

        $this->addColumn('download', [
            'header'    => Mage::helper('backup')->__('Download'),
            'format'    => '<a href="' . $this->getUrl('*/*/download', ['time' => '$time', 'type' => '$type'])
                . '">$extension</a> &nbsp; <small>('.$url7zip.')</small>',
            'index'     => 'type',
            'sortable'  => false,
            'filter'    => false
        ]);

        if (Mage::helper('backup')->isRollbackAllowed()){
            $this->addColumn('action', [
                    'header'   => Mage::helper('backup')->__('Action'),
                    'type'     => 'action',
                    'width'    => '80px',
                    'filter'   => false,
                    'actions'  => [[
                        'url'     => '#',
                        'caption' => Mage::helper('backup')->__('Rollback'),
                        'onclick' => 'return backup.rollback(\'$type\', \'$time\');'
                    ]],
                    'index'    => 'type',
                    'sortable' => false
            ]);
        }

        return $this;
    }
}
