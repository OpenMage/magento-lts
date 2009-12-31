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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var giftMessagesController = {
    toogleRequired: function(source, objects)
    {
        if(!$(source).value.blank()) {
            objects.each(function(item) {
               $(item).addClassName('required-entry');
            });
        } else {
            objects.each(function(item) {
                if($(source).formObj && $(source).formObj.validator) {
                    $(source).formObj.validator.reset(item);
                }
                $(item).removeClassName('required-entry');
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
            this.toogleRequired(this.getFieldId(container, 'message'), [
                this.getFieldId(container, 'sender'),
                this.getFieldId(container, 'recipient')
            ]);
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
        this.toogleRequired(this.getFieldId(container, 'message'), [
            this.getFieldId(container, 'sender'),
            this.getFieldId(container, 'recipient')
        ]);

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

