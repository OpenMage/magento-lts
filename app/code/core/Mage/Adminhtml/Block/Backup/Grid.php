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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml backups grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('backupsGrid');
        $this->setDefaultSort('time', 'desc');
    }

    /**
     * Init backups collection
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
     * @return Mage_Adminhtml_Block_Backup_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('adminhtml')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('backup')->__('Are you sure you want to delete the selected backup(s)?')
        ));

        return $this;
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Backup_Grid
     */
    protected function _prepareColumns()
    {
        $url7zip = Mage::helper('adminhtml')->__('The archive can be uncompressed with <a href="%s">%s</a> on Windows systems', 'http://www.7-zip.org/', '7-Zip');

        $this->addColumn('time', array(
            'header'    => Mage::helper('backup')->__('Time'),
            'index'     => 'date_object',
            'type'      => 'datetime',
            'width'     => 200
        ));

        $this->addColumn('display_name', array(
            'header'    => Mage::helper('backup')->__('Name'),
            'index'     => 'display_name',
            'filter'    => false,
            'sortable'  => true,
            'width'     => 350
        ));

        $this->addColumn('size', array(
            'header'    => Mage::helper('backup')->__('Size, Bytes'),
            'index'     => 'size',
            'type'      => 'number',
            'sortable'  => true,
            'filter'    => false
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('backup')->__('Type'),
            'type'      => 'options',
            'options'   => Mage::helper('backup')->getBackupTypes(),
            'index'     => 'type',
            'width'     => 300
        ));

        $this->addColumn('download', array(
            'header'    => Mage::helper('backup')->__('Download'),
            'format'    => '<a href="' . $this->getUrl('*/*/download', array('time' => '$time', 'type' => '$type'))
                . '">$extension</a> &nbsp; <small>('.$url7zip.')</small>',
            'index'     => 'type',
            'sortable'  => false,
            'filter'    => false
        ));

        if (Mage::helper('backup')->isRollbackAllowed()){
            $this->addColumn('action', array(
                    'header'   => Mage::helper('backup')->__('Action'),
                    'type'     => 'action',
                    'width'    => '80px',
                    'filter'   => false,
                    'sortable' => false,
                    'actions'  => array(array(
                        'url'     => '#',
                        'caption' => Mage::helper('backup')->__('Rollback'),
                        'onclick' => 'return backup.rollback(\'$type\', \'$time\');'
                    )),
                    'index'    => 'type',
                    'sortable' => false
            ));
        }

        return $this;
    }

}
