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
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
                        if ($("select_attribute_set").value != "" && $("select_itemtype").value != "")
                        {
                            var blocksCount = Element.select($("attributes_details"), "div[id^=gbase_attribute_]").length;
                            if (blocksCount > 0 && confirm("'.$this->__('Current Mapping will be reloaded. Continue?').'") || blocksCount == 0)
                            {
                                var elements = [$("select_attribute_set"),$("select_itemtype")].flatten();
                                 $(\'save_button\').disabled = true;
                                new Ajax.Updater("attributes_details", "'.$this->getUrl('*/*/loadAttributes').'", {parameters:Form.serializeElements(elements), evalScripts:true,  onComplete:function(){ $(\'save_button\').disabled = false; } });
                            }
                        }
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
           });
        ';
    }

    public function getHeaderText()
    {
        if(!is_null(Mage::registry('current_item_type')->getId())) {
            return $this->__('Edit Item Type "%s"', $this->htmlEscape(Mage::registry('current_item_type')->getGbaseItemtype()));
        } else {
            return $this->__('New Item Type');
        }
    }

    public function getHeaderCssClass() {
        return 'icon-head head-customer-groups';
    }

}
