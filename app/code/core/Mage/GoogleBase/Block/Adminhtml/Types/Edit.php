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
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Base Types Mapping form block
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_GoogleBase_Block_Adminhtml_Types_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'googlebase';
        $this->_controller = 'adminhtml_types';
        $this->_mode = 'edit';
        $model = Mage::registry('current_item_type');
        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', $this->__('Save Mapping'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', $this->__('Delete Mapping'));
        if(!$model->getId()) {
            $this->_removeButton('delete');
        }

        $this->_formInitScripts[] = '
            var itemType = function() {
                return {
                    updateAttributes: function() {
                        if ($("select_attribute_set").value != ""
                            && $("select_itemtype").value != ""
                            && itemType.confirmChanges()
                        ) {
                            var elements = [
                                $("select_attribute_set"),
                                $("select_itemtype"),
                                $("select_target_country")
                            ].flatten();
                            $(\'save_button\').disabled = true;
                            new Ajax.Updater("attributes_details", "' . $this->getUrl('*/*/loadAttributes') . '",
                                {
                                    parameters:Form.serializeElements(elements),
                                    evalScripts:true,
                                    onComplete:function(){ $(\'save_button\').disabled = false; }
                                }
                            );
                        }
                    },

                    reloadItemTypes: function() {
                        if ($("select_target_country").value != "" && itemType.confirmChanges())
                        {
                            var elements = [
                                $("select_attribute_set"),
                                $("select_itemtype"),
                                $("select_target_country")
                            ].flatten();
                            new Ajax.Updater("gbase_itemtype_select", "' . $this->getUrl('*/*/loadItemTypes') . '",
                                {
                                    parameters:Form.serializeElements(elements),
                                    evalScripts:true,
                                    onComplete:function(){
                                        $(\'save_button\').disabled = false;
                                        Event.observe($("select_itemtype"), \'change\', itemType.updateAttributes);
                                    }
                                }
                            );

                            new Ajax.Updater("attribute_set_select", "' . $this->getUrl('*/*/loadAttributeSets') . '",
                                {
                                    parameters:Form.serializeElements(elements),
                                    evalScripts:true,
                                    onComplete:function(){
                                        $(\'save_button\').disabled = false;
                                        Event.observe($("select_attribute_set"), \'change\', itemType.updateAttributes);
                                    }
                                }
                            );
                            $("attributes_details").innerHTML = "' . Mage::helper('core')->jsQuoteEscape($this->__('Please, select Attribute Set and Google Item Type to load attributes')) . '";
                        }
                    },

                    confirmChanges: function() {
                        var blocksCount = Element.select($("attributes_details"), "div[id^=gbase_attribute_]").length;
                        if (blocksCount > 0
                            && confirm(\'' . Mage::helper('core')->jsQuoteEscape($this->__('Current Mapping will be reloaded. Continue?')) .'\')
                            || blocksCount == 0
                        ) {
                            return true;
                        }
                        return false;
                    }
                }
            }();

             Event.observe(window, \'load\', function(){
                 if ($("select_attribute_set")) {
                     Event.observe($("select_attribute_set"), \'change\', itemType.updateAttributes);
                 }
                 if ($("select_itemtype")) {
                     Event.observe($("select_itemtype"), \'change\', itemType.updateAttributes);
                 }
                 if ($("select_target_country")) {
                     Event.observe($("select_target_country"), \'change\', itemType.reloadItemTypes);
                 }
           });
        ';
    }

    public function getHeaderText()
    {
        if(!is_null(Mage::registry('current_item_type')->getId())) {
            return $this->__('Edit Item Type "%s"', $this->escapeHtml(Mage::registry('current_item_type')->getGbaseItemtype()));
        } else {
            return $this->__('New Item Type');
        }
    }

    public function getHeaderCssClass() {
        return 'icon-head head-customer-groups';
    }

}
