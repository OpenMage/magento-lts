<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Review edit form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'review';

        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', Mage::helper('review')->__('Save Review'));
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'id', 'save_button');
        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'label', Mage::helper('review')->__('Delete Review'));

        if ($this->getRequest()->getParam('productId', false)) {
            $this->_updateButton(
                self::BUTTON_TYPE_BACK,
                'onclick',
                Mage::helper('core/js')->getSetLocationJs(
                    $this->getUrl(
                        '*/catalog_product/edit',
                        ['id' => $this->getRequest()->getParam('productId', false)],
                    ),
                ),
            );
        }

        if ($this->getRequest()->getParam('customerId', false)) {
            $this->_updateButton(
                self::BUTTON_TYPE_BACK,
                'onclick',
                Mage::helper('core/js')->getSetLocationJs(
                    $this->getUrl(
                        '*/customer/edit',
                        ['id' => $this->getRequest()->getParam('customerId', false)],
                    ),
                ),
            );
        }

        if ($this->getRequest()->getParam('ret', false) == 'pending') {
            $this->_updateButton(
                self::BUTTON_TYPE_BACK,
                'onclick',
                Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/pending')),
            );
            $this->_updateButton(
                self::BUTTON_TYPE_DELETE,
                'onclick',
                Mage::helper('core/js')->getDeleteConfirmJs(
                    $this->getUrl(
                        '*/*/delete',
                        [
                            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                            'ret'           => 'pending',
                        ],
                    ),
                ),
            );
            Mage::register('ret', 'pending');
        }

        if ($this->getRequest()->getParam($this->_objectId)) {
            $reviewData = Mage::getModel('review/review')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('review_data', $reviewData);
        }

        $this->_formInitScripts[] = '
            var review = {
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
                    fetch("' . $this->getUrl('*/*/ratingItems', ['_current' => true]) . '", {
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
                }
            };
            window.addEventListener("load", function() {
                var selectStores = document.getElementById("select_stores");
                if (selectStores) {
                    selectStores.addEventListener("change", review.updateRating);
                }
            });
        ';
    }

    /**
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        if (Mage::registry('review_data') && Mage::registry('review_data')->getId()) {
            return Mage::helper('review')->__("Edit Review '%s'", $this->escapeHtml(Mage::registry('review_data')->getTitle()));
        }

        return Mage::helper('review')->__('New Review');
    }
}
