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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installed Extensions Grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Extensions_Remote_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setId('packagesGrid');
        $this->setEmptyText(Mage::helper('adminhtml')->__('No Extensions Found'));
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('adminhtml/extension_remote_collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $ext = Mage::getModel('adminhtml/extension');

        $this->addColumn('channel', array(
            'header'=>Mage::helper('adminhtml')->__('Channel'),
           	'index'=>'channel',
           	#'type'=>'options',
           	#'options'=>$ext->getKnownChannels(),
        ));

        $this->addColumn('category', array(
            'header'=>Mage::helper('adminhtml')->__('Category'),
           	'index'=>'category',
        ));

        $this->addColumn('name', array(
            'header'=>Mage::helper('adminhtml')->__('Extension Name'),
           	'index'=>'name',
        ));

        $this->addColumn('summary', array(
            'header'=>Mage::helper('adminhtml')->__('Summary'),
           	'index'=>'summary',
        ));

        $this->addColumn('remote_version', array(
            'header'=>Mage::helper('adminhtml')->__('Available Version'),
           	'index'=>'remote_version',
           	'type'=>'range',
           	'width'=>'140px',
        ));

        $this->addColumn('local_version', array(
            'header'=>Mage::helper('adminhtml')->__('Installed Version'),
           	'index'=>'local_version',
           	'type'=>'range',
           	'width'=>'140px',
        ));
/*
        $this->addColumn('action',
            array(
                'header'=>Mage::helper('adminhtml')->__('Action'),
                'index'=>'template_id',
                'sortable'=>false,
                'filter' => false,
                'width'	   => '170px',
                'renderer' => 'adminhtml/extensions_remote_grid_renderer_action'
        ));

        $this->addColumn('stability', array(
            'header'=>Mage::helper('adminhtml')->__('Stability'),
           	'index'=>'stability',
           	'type'=>'options',
           	'options'=>$ext->getStabilityOptions(),
        ));

*/
        return $this;
    }

    protected function _prepareMassaction()
    {
        return $this;

        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('package');

        $this->getMassactionBlock()->addItem('install', array(
             'label'=> $this->__('Install'),
             'url'  => $this->getUrl('*/*/massInstall'),
             'confirm' => $this->__('Are you sure you wish to INSTALL all selected packages?')
        ));

        $this->getMassactionBlock()->addItem('upgrade', array(
             'label'=> $this->__('Upgrade'),
             'url'  => $this->getUrl('*/*/massUpgrade'),
             'confirm' => $this->__('Are you sure you wish to UPGRADE all selected packages?')
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        $url = Mage::getModel('adminhtml/url');
        $url->setQueryParam('id', $row->getId());
        return $url->getUrl('*/*/edit');
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}

