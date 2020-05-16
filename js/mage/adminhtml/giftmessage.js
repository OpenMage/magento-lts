/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var giftMessagesController = {
    toogleRequired: function(source, objects)
    {
        if(!$(source).value.blank()) {
            objects.each(function(item) {
               $(item).addClassName('required-entry');
               var label = findFieldLabel($(item));
               if (label) {
                   var span = label.down('span');
                   if (!span) {
                       Element.insert(label, {bottom: '&nbsp;<span class="required">*</span>'});
                   }
               }
            });
        } else {
            objects.each(function(item) {
                if($(source).formObj && $(source).formObj.validator) {
                    $(source).formObj.validator.reset(item);
                }
                $(item).removeClassName('required-entry');
                var label = findFieldLabel($(item));
                if (label) {
                    var span = label.down('span');
                    if (span) {
                        Element.remove(span);
                    }
                }
                // Hide validation advices if exist
                if ($(item) && $(item).advices) {
                    $(item).advices.each(function (pair) {
                        if (pair.value != null) pair.value.hide();
                    });
                }
            });
        }
    },
    toogleGiftMessage: function(container) {
        if(!$(container).toogleGiftMessage) {
            $(container).toogleGiftMessage = true;
            $(this.getFieldId(container, 'edit')).show();
            $(container).down('.action-link').addClassName('open');
            $(container).down('.default-text').hide();
            $(container).down('.close-text').show();
        } else {
            $(container).toogleGiftMessage = false;
            $(this.getFieldId(container, 'message')).formObj = $(this.getFieldId(container, 'form'));

            if(!$(this.getFieldId(container, 'form')).validator) {
                $(this.getFieldId(container, 'form')).validator = new Validation(this.getFieldId(container, 'form'));
            }

            if(!$(this.getFieldId(container, 'form')).validator.validate()) {
                return false;
            }

            new Ajax.Request($(this.getFieldId(container, 'form')).action, {
                parameters: Form.serialize($(this.getFieldId(container, 'form')), true),
                loaderArea: container,
                onComplete: function(transport) {

                    $(container).down('.action-link').removeClassName('open');
                    $(container).down('.default-text').show();
                    $(container).down('.close-text').hide();
                    $(this.getFieldId(container, 'edit')).hide();
                    if (transport.responseText.match(/YES/g)) {
                        $(container).down('.default-text').down('.edit').show();
                        $(container).down('.default-text').down('.add').hide();
                    } else {
                        $(container).down('.default-text').down('.add').show();
                        $(container).down('.default-text').down('.edit').hide();
                    }

                }.bind(this)
            });
        }

        return false;
    },
    saveGiftMessage: function(container) {
        $(this.getFieldId(container, 'message')).formObj = $(this.getFieldId(container, 'form'));

        if(!$(this.getFieldId(container, 'form')).validator) {
            $(this.getFieldId(container, 'form')).validator = new Validation(this.getFieldId(container, 'form'));
        }

        if(!$(this.getFieldId(container, 'form')).validator.validate()) {
            return;
        }

        new Ajax.Request($(this.getFieldId(container, 'form')).action, {
            parameters: Form.serialize($(this.getFieldId(container, 'form')), true),
            loaderArea: container
        });
    },
    getFieldId: function(container, name) {
        return container + '_' + name;
    }
};

function findFieldLabel(field) {
    var tdField = $(field).up('td');
    if (tdField) {
       var tdLabel = tdField.previous('td');
       if (tdLabel) {
           var label = tdLabel.down('label');
           if (label) {
               return label;
           }
       }
    }

    return false;
}


/********************* GIFT OPTIONS POPUP ***********************/
var GiftOptionsPopup = Class.create();
GiftOptionsPopup.prototype = {
    giftOptionsWindowMask: null,
    giftOptionsWindow: null,

    initialize: function() {
        $$('.action-link').each(function (el) {
            Event.observe(el, 'click', this.showItemGiftOptions.bind(this));
        }, this);

        // Move giftcard popup to start of body, because soon it will contain FORM tag that can break DOM layout if within other FORM
        var oldPopupContainer = $('gift_options_configure');
        if (oldPopupContainer) {
            oldPopupContainer.remove();
        }

        var newPopupContainer = $('gift_options_configure_new');
        $(document.body).insert({top: newPopupContainer});
        newPopupContainer.id = 'gift_options_configure';

        // Put controls container inside a FORM tag so we can use Validator
        var form = new Element('form', {action: '#', id: 'gift_options_configuration_form', method: 'post'});
        var formContents = $('gift_options_form_contents');
        if (formContents) {
            formContents.parentNode.appendChild(form);
            form.appendChild(formContents);
        }
    },

    showItemGiftOptions : function(event) {
        var element = Event.element(event).id;
        var itemId = element.sub('gift_options_link_','');

        toggleSelectsUnderBlock(this.giftOptionsWindowMask, false);
        this.giftOptionsWindowMask = $('gift_options_window_mask');
        this.giftOptionsWindow = $('gift_options_configure');
        this.giftOptionsWindow.select('select').each(function(el){
            el.style.visibility = 'visible';
        });

        this.giftOptionsWindowMask.setStyle({'height': $('html-body').getHeight() + 'px'}).show();
        this.giftOptionsWindow.setStyle({'marginTop': -this.giftOptionsWindow.getHeight()/2 + 'px', 'display': 'block'});
        this.setTitle(itemId);

        Event.observe($('gift_options_cancel_button'), 'click', this.onCloseButton.bind(this));
        Event.observe($('gift_options_ok_button'), 'click', this.onOkButton.bind(this));
        Event.stop(event);
    },

    setTitle : function (itemId) {
        var productTitleElement = $('order_item_' + itemId + '_title');
        var productTitle = '';
        if (productTitleElement) {
            productTitle = productTitleElement.innerHTML;
        }
        $('gift_options_configure_title').update(productTitle);
    },

    onOkButton : function() {
        var giftOptionsForm = new varienForm('gift_options_configuration_form');
        giftOptionsForm.canShowError = true;
        if (!giftOptionsForm.validate()) {
            return false;
        }
        giftOptionsForm.validator.reset();
        this.closeWindow();
        return true;
    },

    onCloseButton : function() {
        this.closeWindow();
    },

    closeWindow : function() {
        toggleSelectsUnderBlock(this.giftOptionsWindowMask, true);
        this.giftOptionsWindowMask.style.display = 'none';
        this.giftOptionsWindow.style.display = 'none';
    }
};


/********************* GIFT OPTIONS SET ***********************/
GiftMessageSet = Class.create();
GiftMessageSet.prototype = {
    destPrefix: 'current_item_giftmessage_',
    sourcePrefix: 'giftmessage_',
    fields: ['sender', 'recipient', 'message'],
    isObserved: false,

    initialize: function() {
        $$('.action-link').each(function (el) {
            Event.observe(el, 'click', this.setData.bind(this));
        }, this);
    },

    setData: function(event) {
        var element = Event.element(event).id;
        this.id = element.sub('gift_options_link_','');

        if ($('gift-message-form-data-' + this.id)) {
            this.fields.each(function(el) {
                if ($(this.sourcePrefix + this.id + '_' + el) && $(this.destPrefix + el)) {
                    $(this.destPrefix + el).value = $(this.sourcePrefix + this.id + '_' + el).value;
                }
            }, this);
            $('gift_options_giftmessage').show();
        } else {
            $('gift_options_giftmessage').hide();
        }

        if (!this.isObserved) {
            Event.observe('gift_options_ok_button', 'click', this.saveData.bind(this));
            this.isObserved = true;
        }
    },

    saveData: function(event){
        this.fields.each(function(el) {
            if ($(this.sourcePrefix + this.id + '_' + el) && $(this.destPrefix + el)) {
                $(this.sourcePrefix + this.id + '_' + el).value = $(this.destPrefix + el).value;
            }
        }, this);
        if ($(this.sourcePrefix + this.id + '_form')) {
            $(this.sourcePrefix + this.id + '_form').request();
        } else if (typeof(order) != 'undefined') {
            var data = order.serializeData('gift_options_data_' + this.id);
            order.loadArea(['items'], true, data.toObject());
        }
    }
};
