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

var widgetTools = {
    getDivHtml: function(id, html) {
        if (!html) html = '';
        return '<div id="' + id + '">' + html + '</div>';
    },

    onAjaxSuccess: function(transport) {
        if (transport.responseText.isJSON()) {
            var response = transport.responseText.evalJSON()
            if (response.error) {
                throw response;
            } else if (response.ajaxExpired && response.ajaxRedirect) {
                setLocation(response.ajaxRedirect);
            }
        }
    }
}

var WysiwygWidget = {};
WysiwygWidget.Widget = Class.create();
WysiwygWidget.Widget.prototype = {

    initialize: function(formEl, widgetEl, widgetOptionsEl, optionsSourceUrl) {
        $(formEl).insert({bottom: widgetTools.getDivHtml(widgetOptionsEl)});
        this.widgetEl = $(widgetEl);
        this.widgetOptionsEl = $(widgetOptionsEl);
        this.optionsUrl = optionsSourceUrl;
        this.optionValues = new Hash({});

        Event.observe(this.widgetEl, "change", this.loadOptions.bind(this));

        this.initOptionValues();
    },

    getOptionsContainerId: function() {
        return this.widgetOptionsEl.id + Base64.idEncode(this.widgetEl.value);
    },

    switchOptionsContainer: function(containerId) {
        $$('#' + this.widgetOptionsEl.id + ' div[id^=' + this.widgetOptionsEl.id + ']').each(function(e) {
            this.disableOptionsContainer(e.id);
        }.bind(this));
        if(containerId != undefined) {
            this.enableOptionsContainer(containerId);
        }
        this._showWidgetDescription();
    },

    enableOptionsContainer: function(containerId) {
        $$('#' + containerId + ' .widget-option').each(function(e) {
            e.removeClassName('skip-submit');
            if (e.hasClassName('obligatory')) {
                e.removeClassName('obligatory');
                e.addClassName('required-entry');
            }
        });
        $(containerId).removeClassName('no-display');
    },

    disableOptionsContainer: function(containerId) {
        if ($(containerId).hasClassName('no-display')) {
            return;
        }
        $$('#' + containerId + ' .widget-option').each(function(e) {
            // Avoid submitting fields of unactive container
            if (!e.hasClassName('skip-submit')) {
                e.addClassName('skip-submit');
            }
            // Form validation workaround for unactive container
            if (e.hasClassName('required-entry')) {
                e.removeClassName('required-entry');
                e.addClassName('obligatory');
            }
        });
        $(containerId).addClassName('no-display');
    },

    // Assign widget options values when existing widget selected in WYSIWYG
    initOptionValues: function() {

        if (!this.wysiwygExists()) {
            return false;
        }

        var e = this.getWysiwygNode();
        if (e != undefined && e.id) {
            var widgetCode = Base64.idDecode(e.id);
            this.optionValues = new Hash({});
            widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function(match){
                if (match[1] == 'type') {
                    this.widgetEl.value = match[2];
                } else {
                    this.optionValues.set(match[1], match[2]);
                }
            }.bind(this));

            this.loadOptions();
        }
    },

    loadOptions: function() {
        if (!this.widgetEl.value) {
            this.switchOptionsContainer();
            return;
        }

        if ($(this.getOptionsContainerId()) != undefined) {
            this.switchOptionsContainer(this.getOptionsContainerId());
            return;
        }

        this._showWidgetDescription();

        var params = {widget_type: this.widgetEl.value, values: this.optionValues};
        new Ajax.Request(this.optionsUrl,
            {
                parameters: {widget: Object.toJSON(params)},
                onSuccess: function(transport) {
                    try {
                        widgetTools.onAjaxSuccess(transport);
                        this.switchOptionsContainer();
                        this.widgetOptionsEl.insert({bottom: widgetTools.getDivHtml(this.getOptionsContainerId(), transport.responseText)});
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            }
        );
    },

    _showWidgetDescription: function() {
        var noteCnt = this.widgetEl.next().down('small');
        var descrCnt = $('widget-description-' + this.widgetEl.selectedIndex);
        if(noteCnt != undefined) {
            var description = (descrCnt != undefined ? descrCnt.innerHTML : '');
            noteCnt.update(descrCnt.innerHTML);
        }
    },

    insertWidget: function() {
        if(editForm.validator && editForm.validator.validate() || !editForm.validator){
            var formElements = [];
            var i = 0;
            $(editForm.formId).getElements().each(function(e) {
                if(!e.hasClassName('skip-submit')) {
                    formElements[i] = e;
                    i++;
                }
            });

            // Add as_is flag to parameters if wysiwyg editor doesn't exist
            var params = Form.serializeElements(formElements);
            if (!this.wysiwygExists()) {
                params = params + '&as_is=1';
            }

            new Ajax.Request($(editForm.formId).readAttribute("action"),
            {
                parameters: params,
                onComplete: function(transport) {
                    try {
                        widgetTools.onAjaxSuccess(transport);
                        this.updateContent(transport.responseText);
                        this.getPopup().close();
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            });
        }
    },

    updateContent: function(content) {
        if (this.wysiwygExists()) {
        	this.getPopup().execCommand("mceInsertContent", false, content);
        	// Refocus in window
        	if (this.getPopup().isWindow) {
        		window.focus();
        	}
        	this.getWysiwyg().focus();
        } else {
            var parent = this.getPopup().opener;
            var textareaId = this.getPopup().name.replace(/widget_window/g, '');
            var textarea = parent.document.getElementById(textareaId);
            updateElementAtCursor(textarea, content, this.getPopup().opener);
        }
    },

    wysiwygExists: function() {
        return (typeof tinyMCEPopup != 'undefined') && (typeof tinyMCEPopup.editor != 'undefined');
    },

    getPopup: function() {
        if (this.wysiwygExists()) {
            return tinyMCEPopup;
        } else {
            return window.self;
        }
    },

    getWysiwyg: function() {
        return tinyMCEPopup.editor;
    },

    getWysiwygNode: function() {
        return tinyMCEPopup.editor.selection.getNode();
    }
}

WysiwygWidget.chooser = Class.create();
WysiwygWidget.chooser.prototype = {

    // HTML element A, on which click event fired when choose a selection
    chooserId: null,

    // Source URL for Ajax requests
    chooserUrl: null,

    // Chooser config
    config: null,

    initialize: function(chooserId, chooserUrl, config) {
        this.chooserId = chooserId;
        this.chooserUrl = chooserUrl;
        this.config = config;
    },

    getResponseContainerId: function() {
        return 'responseCnt' + this.chooserId;
    },

    getChooserControl: function() {
        return $(this.chooserId + 'control');
    },

    getElement: function() {
        return $(this.chooserId + 'value');
    },

    getElementLabel: function() {
        return $(this.chooserId + 'label');
    },

    makeControlOpened: function() {
        this.toggleControl(true);
    },

    makeControlClosed: function() {
        this.toggleControl(false);
    },

    toggleControl: function(opened) {
        this.getChooserControl().down('span').innerHTML = (opened ? this.config.buttons.close : this.config.buttons.open);
        if(opened) {
            this.getChooserControl().addClassName('opened');
        } else {
            this.getChooserControl().removeClassName('opened');
        }
    },

    open: function() {
        this.makeControlOpened();
        $(this.getResponseContainerId()).show();
    },

    close: function() {
        this.makeControlClosed();
        $(this.getResponseContainerId()).hide();
    },

    choose: function(event) {

        // Show or hide chooser content if it was already loaded
        var responseContainerId = this.getResponseContainerId();
        if ($(responseContainerId) != undefined) {
            $(responseContainerId).visible() ? this.close() : this.open();
            return;
        }

        // Otherwise load content from server
        new Ajax.Request(this.chooserUrl,
            {
                parameters: {element_value: this.getElementValue(), element_label: this.getElementLabelText()},
                onSuccess: function(transport) {
                    try {
                        widgetTools.onAjaxSuccess(transport);
                        this.getChooserControl().insert({after: widgetTools.getDivHtml(responseContainerId, transport.responseText)});
                        this.makeControlOpened();
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            }
        );
    },

    getElementValue: function(value) {
        return this.getElement().value;
    },

    getElementLabelText: function(value) {
        return this.getElementLabel().innerHTML;
    },

    setElementValue: function(value) {
        this.getElement().value = value;
    },

    setElementLabel: function(value) {
        this.getElementLabel().innerHTML = value;
    }
}
