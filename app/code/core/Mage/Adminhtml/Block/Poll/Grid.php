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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Poll_Model_Resource_Poll_Collection getCollection()
 */
class Mage_Adminhtml_Block_Poll_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Adminhtml_Block_Poll_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('pollGrid');
        $this->setDefaultSort('poll_title');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('poll/poll')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();

        if (!Mage::app()->isSingleStoreMode()) {
            $this->getCollection()->addStoreData();
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('poll_id', [
            'header'    => Mage::helper('poll')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'poll_id',
        ]);

        $this->addColumn('poll_title', [
            'header'    => Mage::helper('poll')->__('Poll Question'),
            'align'     =>'left',
            'index'     => 'poll_title',
        ]);

        $this->addColumn('votes_count', [
            'header'    => Mage::helper('poll')->__('Number of Responses'),
            'width'     => '50px',
            'type'      => 'number',
            'index'     => 'votes_count',
        ]);

        $this->addColumn('date_posted', [
            'header'    => Mage::helper('poll')->__('Date Posted'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'index'     => 'date_posted',
            'format'	=> Mage::app()->getLocale()->getDateFormat()
        ]);

        $this->addColumn('date_closed', [
            'header'    => Mage::helper('poll')->__('Date Closed'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'date_closed',
            'format'	=> Mage::app()->getLocale()->getDateFormat()
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', [
                'header'    => Mage::helper('review')->__('Visible In'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => true,
                'sortable'   => false,
            ]);
        }

        $this->addColumn('closed', [
            'header'    => Mage::helper('poll')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'closed',
            'type'      => 'options',
            'options'   => [
                1 => Mage::helper('poll')->__('Closed'),
                0 => Mage::helper('poll')->__('Open')
            ],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Poll_Model_Poll $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
