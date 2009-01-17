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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid widget massaction block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract extends Mage_Adminhtml_Block_Widget
{
    /**
     * Massaction items
     *
     * @var array
     */
    protected $_items = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/grid/massaction.phtml');
    }

    /**
     * Add new massaction item
     *
     * $item = array(
     *      'label'    => string,
     *      'complete' => string, // Only for ajax enabled grid (optional)
     *      'url'      => string,
     *      'confirm'  => string, // text of confirmation of this action (optional)
     *      'additional' => string|array|Mage_Core_Block_Abstract // (optional)
     * );
     *
     * @param string $itemId
     * @param array $item
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
     */
    public function addItem($itemId, array $item)
    {
        $this->_items[$itemId] =  $this->getLayout()->createBlock('adminhtml/widget_grid_massaction_item')
            ->setData($item)
            ->setMassaction($this)
            ->setId($itemId);

        if($this->_items[$itemId]->getAdditional()) {
            $this->_items[$itemId]->setAdditionalActionBlock($this->_items[$itemId]->getAdditional());
            $this->_items[$itemId]->unsAdditional();
        }

        return $this;
    }

    /**
     * Retrive massaction item with id $itemId
     *
     * @param string $itemId
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Item
     */
    public function getItem($itemId)
    {
        if(isset($this->_items[$itemId])) {
            return $this->_items[$itemId];
        }

        return null;
    }

    /**
     * Retrive massaction items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Retrive massaction items JSON
     *
     * @return string
     */
    public function getItemsJson()
    {
        $result = array();
        foreach ($this->getItems() as $itemId=>$item) {
            $result[$itemId] = $item->toArray();
        }

        return Zend_Json::encode($result);
    }

    /**
     * Retrive massaction items count
     *
     * @return integer
     */
    public function getCount()
    {
        return sizeof($this->_items);
    }

    /**
     * Checks are massactions available
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->getCount() > 0 && $this->getParentBlock()->getMassactionIdField();
    }

    /**
     * Retrive global form field name for all massaction items
     *
     * @return string
     */
    public function getFormFieldName()
    {
        return ($this->getData('form_field_name') ? $this->getData('form_field_name') : 'massaction');
    }

    /**
     * Retrive form field name for internal use. Based on $this->getFormFieldName()
     *
     * @return string
     */
    public function getFormFieldNameInternal()
    {
        return  'internal_' . $this->getFormFieldName();
    }

    /**
     * Retrive massaction block js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrive grid block js object name
     *
     * @return string
     */
    public function getGridJsObjectName()
    {
        return $this->getParentBlock()->getJsObjectName();
    }

    /**
     * Retrive JSON string of selected checkboxes
     *
     * @return string
     */
    public function getSelectedJson()
    {
        if($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            $selected = explode(',', $selected);
            return Zend_Json::encode($selected);
        } else {
            return '[]';
        }
    }

    /**
     * Retrive array of selected checkboxes
     *
     * @return array
     */
    public function getSelected()
    {
        if($selected = $this->getRequest()->getParam($this->getFormFieldNameInternal())) {
            $selected = explode(',', $selected);
            return $selected;
        } else {
            return array();
        }
    }

    /**
     * Retrive apply button html
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        return $this->getButtonHtml($this->__('Submit'), $this->getJsObjectName() . ".apply()");
    }

    public function getJavaScript()
    {
        return "
                var {$this->getJsObjectName()} = new varienGridMassaction('{$this->getHtmlId()}', {$this->getGridJsObjectName()}, {$this->getSelectedJson()}, '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');
                {$this->getJsObjectName()}.setItems({$this->getItemsJson()});
                {$this->getJsObjectName()}.setGridIds({$this->getGridIdsJson()});
                ". ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '');
    }

    public function getGridIdsJson()
    {
        $gridIds = $this->getParentBlock()->getCollection()->getAllIds();

        if(!empty($gridIds)) {
            return Zend_Json::encode($gridIds);
        }
        return '[]';
    }

    public function getHtmlId()
    {
        return $this->getParentBlock()->getHtmlId() . '_massaction';
    }

}
 // Class Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract End