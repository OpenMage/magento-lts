/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

// Build a form body with isAjax + form_key (previously injected by loader.js's
// Ajax.Request monkey-patch; must be added explicitly for fetch).
function _giftFormBody(formEl) {
    var body = new URLSearchParams(new FormData(formEl));
    body.append('isAjax', 'true');
    if (window.FORM_KEY && !body.has('form_key')) {
        body.append('form_key', window.FORM_KEY);
    }
    return body.toString();
}

var giftMessagesController = {
    toogleRequired: function(source, objects) {
        var sourceEl = document.getElementById(source);
        if (sourceEl.value.trim()) {
            objects.forEach(function(item) {
                var itemEl = document.getElementById(item);
                itemEl.classList.add('required-entry');
                var label = findFieldLabel(itemEl);
                if (label && !label.querySelector('span')) {
                    label.insertAdjacentHTML('beforeend', '&nbsp;<span class="required">*</span>');
                }
            });
        } else {
            objects.forEach(function(item) {
                var itemEl = document.getElementById(item);
                if (sourceEl.formObj && sourceEl.formObj.validator) {
                    sourceEl.formObj.validator.reset(item);
                }
                itemEl.classList.remove('required-entry');
                var label = findFieldLabel(itemEl);
                if (label) {
                    var span = label.querySelector('span');
                    if (span) {
                        span.remove();
                    }
                }
                // Hide validation advices if exist
                if (itemEl && itemEl.advices) {
                    Object.keys(itemEl.advices).forEach(function(key) {
                        var advice = itemEl.advices[key];
                        if (advice != null) advice.style.display = 'none';
                    });
                }
            });
        }
    },
    toogleGiftMessage: function(container) {
        var containerEl = document.getElementById(container);
        if (!containerEl.toogleGiftMessage) {
            containerEl.toogleGiftMessage = true;
            document.getElementById(this.getFieldId(container, 'edit')).style.display = '';
            containerEl.querySelector('.action-link').classList.add('open');
            containerEl.querySelector('.default-text').style.display = 'none';
            containerEl.querySelector('.close-text').style.display = '';
        } else {
            containerEl.toogleGiftMessage = false;
            var formEl = document.getElementById(this.getFieldId(container, 'form'));
            document.getElementById(this.getFieldId(container, 'message')).formObj = formEl;

            if (!formEl.validator) {
                formEl.validator = new Validation(this.getFieldId(container, 'form'));
            }

            if (!formEl.validator.validate()) {
                return false;
            }

            var self = this;
            fetch(formEl.action, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: _giftFormBody(formEl)
            })
            .then(function(resp) { return resp.text(); })
            .then(function(responseText) {
                containerEl.querySelector('.action-link').classList.remove('open');
                containerEl.querySelector('.default-text').style.display = '';
                containerEl.querySelector('.close-text').style.display = 'none';
                document.getElementById(self.getFieldId(container, 'edit')).style.display = 'none';
                if (responseText.match(/YES/g)) {
                    containerEl.querySelector('.default-text .edit').style.display = '';
                    containerEl.querySelector('.default-text .add').style.display = 'none';
                } else {
                    containerEl.querySelector('.default-text .add').style.display = '';
                    containerEl.querySelector('.default-text .edit').style.display = 'none';
                }
            });
        }
        return false;
    },
    saveGiftMessage: function(container) {
        var formEl = document.getElementById(this.getFieldId(container, 'form'));
        document.getElementById(this.getFieldId(container, 'message')).formObj = formEl;

        if (!formEl.validator) {
            formEl.validator = new Validation(this.getFieldId(container, 'form'));
        }

        if (!formEl.validator.validate()) {
            return;
        }

        showLoader();
        fetch(formEl.action, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: _giftFormBody(formEl)
        }).finally(function() {
            hideLoader();
        });
    },
    getFieldId: function(container, name) {
        return container + '_' + name;
    }
};

function findFieldLabel(field) {
    var tdField = field.closest('td');
    if (tdField) {
        var tdLabel = tdField.previousElementSibling;
        if (tdLabel) {
            var label = tdLabel.querySelector('label');
            if (label) {
                return label;
            }
        }
    }
    return false;
}


/********************* GIFT OPTIONS POPUP ***********************/
function GiftOptionsPopup() {
    if (this.initialize) this.initialize.apply(this, arguments);
}
GiftOptionsPopup.prototype = {
    giftOptionsWindowMask: null,
    giftOptionsWindow: null,

    initialize: function() {
        document.querySelectorAll('.action-link').forEach(function(el) {
            el.addEventListener('click', this.showItemGiftOptions.bind(this));
        }.bind(this));

        // Move giftcard popup to start of body, because soon it will contain FORM tag that can break DOM layout if within other FORM
        var oldPopupContainer = document.getElementById('gift_options_configure');
        if (oldPopupContainer) {
            oldPopupContainer.remove();
        }

        var newPopupContainer = document.getElementById('gift_options_configure_new');
        document.body.insertBefore(newPopupContainer, document.body.firstChild);
        newPopupContainer.id = 'gift_options_configure';

        // Put controls container inside a FORM tag so we can use Validator
        var form = document.createElement('form');
        form.action = '#';
        form.id = 'gift_options_configuration_form';
        form.method = 'post';
        var formContents = document.getElementById('gift_options_form_contents');
        if (formContents) {
            formContents.parentNode.appendChild(form);
            form.appendChild(formContents);
        }
    },

    showItemGiftOptions: function(event) {
        var element = event.target.id;
        var itemId = element.replace('gift_options_link_', '');

        this.giftOptionsWindowMask = document.getElementById('gift_options_window_mask');
        this.giftOptionsWindow = document.getElementById('gift_options_configure');
        this.giftOptionsWindow.querySelectorAll('select').forEach(function(el) {
            el.style.visibility = 'visible';
        });

        this.giftOptionsWindowMask.style.height = document.getElementById('html-body').offsetHeight + 'px';
        this.giftOptionsWindowMask.style.display = '';
        this.giftOptionsWindow.style.marginTop = (-this.giftOptionsWindow.offsetHeight / 2) + 'px';
        this.giftOptionsWindow.style.display = 'block';
        this.setTitle(itemId);

        document.getElementById('gift_options_cancel_button').addEventListener('click', this.onCloseButton.bind(this));
        document.getElementById('gift_options_ok_button').addEventListener('click', this.onOkButton.bind(this));
        event.preventDefault();
        event.stopPropagation();
    },

    setTitle: function(itemId) {
        var productTitleElement = document.getElementById('order_item_' + itemId + '_title');
        document.getElementById('gift_options_configure_title').innerHTML = productTitleElement ? productTitleElement.innerHTML : '';
    },

    onOkButton: function() {
        var giftOptionsForm = new varienForm('gift_options_configuration_form');
        giftOptionsForm.canShowError = true;
        if (!giftOptionsForm.validate()) {
            return false;
        }
        giftOptionsForm.validator.reset();
        this.closeWindow();
        return true;
    },

    onCloseButton: function() {
        this.closeWindow();
    },

    closeWindow: function() {
        this.giftOptionsWindowMask.style.display = 'none';
        this.giftOptionsWindow.style.display = 'none';
    }
};


/********************* GIFT OPTIONS SET ***********************/
function GiftMessageSet() {
    if (this.initialize) this.initialize.apply(this, arguments);
}
GiftMessageSet.prototype = {
    destPrefix: 'current_item_giftmessage_',
    sourcePrefix: 'giftmessage_',
    fields: ['sender', 'recipient', 'message'],
    isObserved: false,

    initialize: function() {
        document.querySelectorAll('.action-link').forEach(function(el) {
            el.addEventListener('click', this.setData.bind(this));
        }.bind(this));
    },

    setData: function(event) {
        var element = event.target.id;
        this.id = element.replace('gift_options_link_', '');

        if (document.getElementById('gift-message-form-data-' + this.id)) {
            var self = this;
            this.fields.forEach(function(el) {
                var src = document.getElementById(self.sourcePrefix + self.id + '_' + el);
                var dst = document.getElementById(self.destPrefix + el);
                if (src && dst) {
                    dst.value = src.value;
                }
            });
            document.getElementById('gift_options_giftmessage').style.display = '';
        } else {
            document.getElementById('gift_options_giftmessage').style.display = 'none';
        }

        if (!this.isObserved) {
            document.getElementById('gift_options_ok_button').addEventListener('click', this.saveData.bind(this));
            this.isObserved = true;
        }
    },

    saveData: function(event) {
        var self = this;
        this.fields.forEach(function(el) {
            var src = document.getElementById(self.sourcePrefix + self.id + '_' + el);
            var dst = document.getElementById(self.destPrefix + el);
            if (src && dst) {
                src.value = dst.value;
            }
        });
        var formEl = document.getElementById(this.sourcePrefix + this.id + '_form');
        if (formEl) {
            fetch(formEl.action, {
                method: (formEl.method || 'post').toUpperCase(),
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: _giftFormBody(formEl)
            });
        } else if (typeof(order) != 'undefined') {
            var data = order.serializeData('gift_options_data_' + this.id);
            order.loadArea(['items'], true, data);
        }
    }
};
