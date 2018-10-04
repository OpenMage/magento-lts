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
 * Poll answers grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Poll_Edit_Tab_Answers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('answersGrid');
        $this->setDefaultSort('answer_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('poll/poll_answer')
            ->getResourceCollection()
            ->addPollFilter($this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('answer_id', array(
            'header'    => Mage::helper('poll')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'answer_id',
        ));

        $this->addColumn('answer_title', array(
            'header'    => Mage::helper('poll')->__('Answer Title'),
            'align'     =>'left',
            'index'     => 'answer_title',
        ));

        $this->addColumn('votes_count', array(
            'header'    => Mage::helper('poll')->__('Votes Count'),
            'type'      => 'number',
            'width'     => '50px',
            'index'     => 'votes_count',
        ));

        $this->addColumn('actions', array(
            'header'    => Mage::helper('poll')->__('Actions'),
            'align'     => 'center',
            'type'      => 'action',
            'width'     => '10px',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('poll')->__('Delete'),
                    'onClick'   => 'return answers.delete(\'$answer_id\')',
                    'url'       => '#',
                ),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/poll_answer/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/poll_answer/grid', array('id' => $this->getRequest()->getParam('id')));
    }

}
