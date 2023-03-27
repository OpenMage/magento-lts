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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Rating_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ratingsGrid');
        $this->setDefaultSort('rating_code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter(Mage::registry('entityId'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Rating Grid colunms
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rating_id', [
            'header'    => Mage::helper('rating')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'rating_id',
        ]);

        $this->addColumn('rating_code', [
            'header'    => Mage::helper('rating')->__('Rating Name'),
            'index'     => 'rating_code',
        ]);

        $this->addColumn('position', [
            'header' => Mage::helper('rating')->__('Sort Order'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'position',
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
