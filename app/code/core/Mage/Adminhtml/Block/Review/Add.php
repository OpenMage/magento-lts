<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml add Review main block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'review';
        $this->_mode = 'add';

        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', Mage::helper('review')->__('Save Review'));
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'id', 'save_button');

        $this->_updateButton(self::BUTTON_TYPE_RESET, 'id', 'reset_button');

        $this->_formScripts[] = '
            toggleParentVis("add_review_form");
            toggleVis("save_button");
            toggleVis("reset_button");
        ';

        $this->_formInitScripts[] = '
            //<![CDATA[
            var review = function() {
                return {
                    productInfoUrl: null,
                    formHidden: true,

                    gridRowClick: function(data, click) {
                        var tr = (click.target || click.srcElement).closest("TR");
                        if (tr && tr.title) {
                            review.productInfoUrl = tr.title;
                            review.loadProductData();
                            review.showForm();
                            review.formHidden = false;
                        }
                    },

                    loadProductData: function() {
                        fetch(review.productInfoUrl, {
                            method: "POST",
                            headers: {"Content-Type": "application/x-www-form-urlencoded", "X-Requested-With": "XMLHttpRequest"},
                            body: new URLSearchParams({form_key: window.FORM_KEY}).toString()
                        }).then(function(resp) { return resp.text(); }).then(function(text) {
                            review.reqSuccess({responseText: text});
                        });
                    },

                    showForm: function() {
                        toggleParentVis("add_review_form");
                        toggleVis("reviewProductGrid");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },

                    updateRating: function() {
                        var selectEl = document.getElementById("select_stores");
                        var params = new URLSearchParams();
                        if (selectEl) {
                            Array.from(selectEl.options).forEach(function(opt) {
                                if (opt.selected) { params.append(selectEl.name, opt.value); }
                            });
                        }
                        document.querySelectorAll("#rating_detail input[type=radio]").forEach(function(el) {
                            if (el.checked) { params.append(el.name, el.value); }
                        });
                        params.append("form_key", window.FORM_KEY);
                        document.getElementById("save_button").disabled = true;
                        fetch("' . $this->getUrl('*/*/ratingItems') . '", {
                            method: "POST",
                            headers: {"Content-Type": "application/x-www-form-urlencoded", "X-Requested-With": "XMLHttpRequest"},
                            body: params.toString()
                        }).then(function(resp) { return resp.text(); }).then(function(html) {
                            var container = document.getElementById("rating_detail");
                            container.innerHTML = html;
                            container.querySelectorAll("script").forEach(function(s) {
                                var ns = document.createElement("script");
                                ns.textContent = s.textContent;
                                document.head.appendChild(ns);
                            });
                            document.getElementById("save_button").disabled = false;
                        });
                    },

                    reqSuccess: function(o) {
                        var response;
                        try { response = JSON.parse(o.responseText); } catch(e) { return; }
                        if (response.error) {
                            alert(response.message);
                        } else if (response.id) {
                            document.getElementById("product_id").value = response.id;
                            var link = document.createElement("a");
                            link.href = "' . $this->getUrl('*/catalog_product/edit') . '" + "id/" + response.id;
                            link.target = "_blank";
                            link.textContent = response.name;
                            var pname = document.getElementById("product_name");
                            pname.innerHTML = "";
                            pname.appendChild(link);
                        } else if (response.message) {
                            alert(response.message);
                        }
                    }
                };
            }();

            window.addEventListener("load", function() {
                var selectStores = document.getElementById("select_stores");
                if (selectStores) {
                    selectStores.addEventListener("change", review.updateRating);
                }
            });
            //]]>
        ';
    }

    /**
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        return Mage::helper('review')->__('Add New Review');
    }
}
