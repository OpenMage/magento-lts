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
 * Adminhtml  system templates grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Email_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setEmptyText(Mage::helper('adminhtml')->__('No Templates Found'));
        $this->setId('systemEmailTemplateGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('core/email_template_collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'template_id',
            [
                  'header' => Mage::helper('adminhtml')->__('ID'),
                  'index' => 'template_id'
            ]
        );

        $this->addColumn(
            'code',
            [
                'header' => Mage::helper('adminhtml')->__('Template Name'),
                'index' => 'template_code'
            ]
        );

        $this->addColumn(
            'added_at',
            [
                'header' => Mage::helper('adminhtml')->__('Date Added'),
                'index' => 'added_at',
                'gmtoffset' => true,
                'type' => 'datetime'
            ]
        );

        $this->addColumn(
            'modified_at',
            [
                'header' => Mage::helper('adminhtml')->__('Date Updated'),
                'index' => 'modified_at',
                'gmtoffset' => true,
                'type' => 'datetime'
            ]
        );

        $this->addColumn(
            'subject',
            [
                'header' => Mage::helper('adminhtml')->__('Subject'),
                'index' => 'template_subject'
            ]
        );
        /*
        $this->addColumn('sender',
            array(
                'header'=>Mage::helper('adminhtml')->__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/system_email_template_grid_renderer_sender'
        ));
        */
        $this->addColumn(
            'type',
            [
                'header' => Mage::helper('adminhtml')->__('Template Type'),
                'index' => 'template_type',
                'filter' => 'adminhtml/system_email_template_grid_filter_type',
                'renderer' => 'adminhtml/system_email_template_grid_renderer_type'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'    => Mage::helper('adminhtml')->__('Action'),
                'index'     => 'template_id',
                'sortable'  => false,
                'filter'    => false,
                'width'     => '100px',
                'renderer'  => 'adminhtml/system_email_template_grid_renderer_action'
            ]
        );
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
