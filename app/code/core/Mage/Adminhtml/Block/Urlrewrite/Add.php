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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml urlrewrite add block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Urlrewrite_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'urlrewrite';
        $this->_mode = 'add';

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save Url'));
        $this->_updateButton('save', 'id', 'save_button');

        $this->_updateButton('reset', 'id', 'reset_button');

        $this->_formScripts[] = '
            toggleFieldsetVis("add_urlrewrite_form");
            toggleVis("products_grid");
            toggleVis("category_tree");

            //toggleParentVis("add_urlrewrite_form");
            //toggleParentVis("products_grid");
            //toggleParentVis("category_tree");
            toggleVis("save_button");
            toggleVis("reset_button");
            document.getElementById("urlrewrite_container").style.display="block";
        ';

        $this->_formInitScripts[] = '
            //<![CDATA[
            var urlrewrite = function() {
                return {
                    productInfoUrl : null,
                    formHidden : true,
					cateoryInfoUrl : null,
                    gridRowClick : function(data, click) {

                        if(Event.findElement(click,\'TR\').id){
                            urlrewrite.productInfoUrl = Event.findElement(click,\'TR\').id;
                            urlrewrite.loadProductData();
                            urlrewrite.showForm();
                            urlrewrite.formHidden = false;
                        }
                    },

                    loadProductData : function() {

                    	urlrewrite.categoryInfoUrl = urlrewrite.productInfoUrl.replace("jsonProductInfo","getCategoryInfo");
                        var con = new Ext.lib.Ajax.request(\'POST\', urlrewrite.productInfoUrl, {success:urlrewrite.reqSuccess,failure:urlrewrite.reqFailure});
                    },

                    showForm : function() {
                        toggleVis("products_grid");
                        toggleVis("category_tree");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },

                    showForm1 : function() {
                        toggleFieldsetVis("add_urlrewrite_form");
                        toggleVis("category_tree");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },

                    updateRating: function() {
                        var typeDom = $("type");
                        // 2 : product
                        if (typeDom.options[typeDom.options.selectedIndex].value == 1) {

                            urlrewrite.categoryInfoUrl = "' . $this->getUrl('*/urlrewrite/getCategoryInfo') . '";
                            var con = new Ext.lib.Ajax.request(\'POST\', urlrewrite.categoryInfoUrl, {success:urlrewrite.loadCategory,failure:urlrewrite.reqFailure});
                        	toggleVis("category_tree");
                        	toggleFieldsetVis("add_urlrewrite_type");
                        } else if (typeDom.options[typeDom.options.selectedIndex].value == 2) {
                        	toggleVis("products_grid");
                        	toggleFieldsetVis("add_urlrewrite_type");
                        } else if (typeDom.options[typeDom.options.selectedIndex].value == 3) {
                        	toggleFieldsetVis("add_urlrewrite_form");
                        	toggleFieldsetVis("add_urlrewrite_type");
                        	toggleVis("save_button");
                        	toggleVis("reset_button");
                        	toggleElements($("add_urlrewrite_form"), ["product_name","category_name"]);
                        }
                    },

                    reqSuccess :function(o) {
                        var response = Ext.util.JSON.decode(o.responseText);
                        if( response.error ) {
                            alert(response.message);
                        } else if( response.id ){
                            $("product_id").value = response.id;

                            $("product_name").innerHTML = \'<a href="' . $this->getUrl('*/catalog_product/edit') . 'id/\' + response.id + \'" target="_blank">\' + response.name + \'<\/a>\';
                            $("id_path").value = "product/" + response.id;
                            $("request_path").value = response.url_key + ".html";
                            $("target_path").value = "catalog/product/view/id/" + response.id;
                            var con = new Ext.lib.Ajax.request(\'POST\', urlrewrite.categoryInfoUrl, {success:urlrewrite.loadCategory,failure:urlrewrite.reqFailure});
                            toggleVis("save_button");
                        	toggleVis("reset_button");
                        } else if( response.message ) {
                            alert(response.message);
                        }
                    },

                    loadCategory: function(o) {
        				if (! o.responseText ) {
        					alert(o.message);
        				} else {
        					var response = Ext.util.JSON.decode(o.responseText);
        					// Create category tree using json data
        					buildCategoryTree(_root, response);
        					// Expand all tree members
        					//_tree.expandAll();
        					// Disable associated categories for current product
        					_tree.disableChecked();
        				}
                    }
                }
            }();

             Event.observe(window, \'load\', function(){
                 Event.observe($("type"), \'change\', urlrewrite.updateRating);
                 $$(\'.content-header-floating\').each(function (el) { el.remove(); });
           });

           // toggle element in parent
            function toggleElements(parent, ids) {
                var elems = parent.childElements();
                if (elems.length > 0) for (var idx in elems) {
                    if (idx.match(/[0-9]/)) {
                        if (elems[idx].id) {
                            if (in_array(elems[idx].id, ids) && elems[idx].parentNode) {
                                $(elems[idx].id).removeClassName("required-entry");
                                elems[idx].parentNode.parentNode.toggle();
                            } else {
                                if (elems[idx].id == "category_name" && elems[idx].parentNode) {
                                    elems[idx].parentNode.parentNode.toggle();
                                } else {
                                    toggleElements(elems[idx], ids);
                                }
                            }
                        } else {
                            toggleElements(elems[idx], ids);
                        }
                    }
                }
            }

            // find string in array
            function in_array(search_term,arrayobj){
                var i = arrayobj.length;
                if (i > 0) {
                    do {
                        if (arrayobj[i] === search_term){
                            return true;
                        }
                    } while (i--);
                }
                return false;
            }

            // trim function for string
            String.prototype.trim = function () { return this.replace(/^\s+|\s+$/g, ""); };
            //]]>
        ';
    }

    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Add New Urlrewrite');
    }
}