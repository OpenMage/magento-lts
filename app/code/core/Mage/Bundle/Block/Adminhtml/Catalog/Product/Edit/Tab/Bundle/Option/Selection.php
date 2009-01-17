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
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle selection renderer
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        $this->setTemplate('bundle/product/edit/bundle/option/selection.phtml');
    }

    public function getFieldId()
    {
        return 'bundle_selection';
    }

    public function getFieldName()
    {
        return 'bundle_selections';
    }

    protected function _prepareLayout()
    {
        $this->setChild('selection_delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete'),
                    'class' => 'delete icon-btn',
                    'on_click' => 'bSelection.remove(event)'
                ))
        );

        return parent::_prepareLayout();
    }

    public function getSelectionDeleteButtonHtml()
    {
        return $this->getChildHtml('selection_delete_button');
    }

    public function getPriceTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id' => $this->getFieldId().'_{{index}}_price_type',
                'class' => 'select select-product-option-type required-option-select'
            ))
            ->setName($this->getFieldName().'[{{parentIndex}}][{{index}}][selection_price_type]')
            ->setOptions(Mage::getSingleton('bundle/source_option_selection_price_type')->toOptionArray());

        return $select->getHtml();
    }

    public function getQtyTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id' => $this->getFieldId().'_{{index}}_can_change_qty',
                'class' => 'select'
            ))
            ->setName($this->getFieldName().'[{{parentIndex}}][{{index}}][selection_can_change_qty]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());

        return $select->getHtml();
    }

    public function getSelectionSearchUrl()
    {
        return $this->getUrl('bundle/selection/search');
    }
}