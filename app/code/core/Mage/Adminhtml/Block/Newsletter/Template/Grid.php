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
 * Adminhtml newsletter templates grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setEmptyText(Mage::helper('newsletter')->__('No Templates Found'));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/template_collection')
            ->useOnlyActual();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'template_code',
            ['header' => Mage::helper('newsletter')->__('ID'), 'align' => 'center', 'index' => 'template_id']
        );
        $this->addColumn(
            'code',
            [
                'header' => Mage::helper('newsletter')->__('Template Name'),
                   'index' => 'template_code'
            ]
        );

        $this->addColumn(
            'added_at',
            [
                'header' => Mage::helper('newsletter')->__('Date Added'),
                'index' => 'added_at',
                'gmtoffset' => true,
                'type' => 'datetime'
            ]
        );

        $this->addColumn(
            'modified_at',
            [
                'header' => Mage::helper('newsletter')->__('Date Updated'),
                'index' => 'modified_at',
                'gmtoffset' => true,
                'type' => 'datetime'
            ]
        );

        $this->addColumn(
            'subject',
            [
                'header' => Mage::helper('newsletter')->__('Subject'),
                'index' => 'template_subject'
            ]
        );

        $this->addColumn(
            'sender',
            [
                'header' => Mage::helper('newsletter')->__('Sender'),
                'index' => 'template_sender_email',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_sender'
            ]
        );

        $this->addColumn(
            'type',
            [
                'header' => Mage::helper('newsletter')->__('Template Type'),
                'index' => 'template_type',
                'type' => 'options',
                'options' => [
                    Mage_Newsletter_Model_Template::TYPE_HTML   => 'html',
                    Mage_Newsletter_Model_Template::TYPE_TEXT   => 'text'
                ],
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'    => Mage::helper('newsletter')->__('Action'),
                'index'     => 'template_id',
                'sortable' => false,
                'filter'   => false,
                'no_link' => true,
                'width'    => '170px',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_action'
            ]
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
