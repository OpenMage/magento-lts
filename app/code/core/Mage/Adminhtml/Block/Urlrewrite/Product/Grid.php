<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Products grid for urlrewrites
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Urlrewrite_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * Disable massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare columns layout
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => Mage::helper('adminhtml')->__('ID'),
                'index' => 'entity_id',
            ],
        );

        $this->addColumn(
            'name',
            [
                'header' => Mage::helper('adminhtml')->__('Name'),
                'index' => 'name',
            ],
        );

        $this->addColumn(
            'sku',
            [
                'header' => Mage::helper('adminhtml')->__('SKU'),
                'width' => 80,
                'index' => 'sku',
            ],
        );
        $this->addColumn(
            'status',
            [
                'header' => Mage::helper('adminhtml')->__('Status'),
                'width' => 50,
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ],
        );
        return $this;
    }

    /**
     * Get url for dispatching grid ajax requests
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', ['_current' => true]);
    }

    /**
     * Get row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['product' => $row->getId()]) . 'category';
    }
}
