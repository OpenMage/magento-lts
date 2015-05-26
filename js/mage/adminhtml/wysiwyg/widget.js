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
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
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
    },

    openDialog: function(widgetUrl) {
        if ($('widget_window') && typeof(Windows) != 'undefined') {
            Windows.focus('widget_window');
            return;
        }
        this.dialogWindow = Dialog.info(null, {
            draggable:true,
            resizable:false,
            closable:true,
            className:'magento',
            windowClassName:"popup-window",
            title:Translator.translate('Insert Widget...'),
            top:50,
            width:950,
            //height:450,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:'widget_window',
            onClose: this.closeDialog.bind(this)
        });
        new Ajax.Updater('modal_dialog_message', widgetUrl, {evalScripts: true});
    },
    closeDialog: function(window) {
        if (!window) {
            window = this.dialogWindow;
        }
        if (window) {
            // IE fix - hidden form select fields after closing dialog
            WindowUtilities._showSelect();
            window.close();
        }
    }
}

var WysiwygWidget = {};
WysiwygWidget.Widget = Class.create();
WysiwygWidget.Widget.prototype = {

    initialize: function(formEl, widgetEl, widgetOptionsEl, optionsSourceUrl, widgetTargetId) {
        $(formEl).insert({bottom: widgetTools.getDivHtml(widgetOptionsEl)});
        this.formEl = formEl;
        this.widgetEl = $(widgetEl);
        this.widgetOptionsEl = $(widgetOptionsEl);
        this.optionsUrl = optionsSourceUrl;
        this.optionValues = new Hash({});
        this.widgetTargetId = widgetTargetId;
        if (typeof(tinyMCE) != "undefined" && tinyMCE.activeEditor) {
            this.bMark = tinyMCE.activeEditor.selection.getBookmark();
        }

        Event.observe(this.widgetEl, "change", this.loadOptions.bind(this));

        this.initOptionValues();
    },

    getOptionsContainerId: function() {
        return this.widgetOptionsEl.id + '_' + this.widgetEl.value.gsub(/\//, '_');
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
            if (widgetCode.indexOf('{{widget') != -1) {
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
        }
    },

    loadOptions: function() {
        if (!this.widgetEl.value) {
            this.switchOptionsContainer();
            return;
        }

        var optionsContainerId = this.getOptionsContainerId();
        if ($(optionsContainerId) != undefined) {
            this.switchOptionsContainer(optionsContainerId);
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
                        if ($(optionsContainerId) == undefined) {
                            this.widgetOptionsEl.insert({bottom: widgetTools.getDivHtml(optionsContainerId, transport.responseText)});
                        } else {
                            this.switchOptionsContainer(optionsContainerId);
                        }
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
        widgetOptionsForm = new varienForm(this.formEl);
        if(widgetOptionsForm.validator && widgetOptionsForm.validator.validate() || !widgetOptionsForm.validator){
            var formElements = [];
            var i = 0;
            Form.getElements($(this.formEl)).each(function(e) {
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

            new Ajax.Request($(this.formEl).action,
            {
                parameters: params,
                onComplete: function(transport) {
                    try {
                        widgetTools.onAjaxSuccess(transport);
                        Windows.close("widget_window");

                        if (typeof(tinyMCE) != "undefined" && tinyMCE.activeEditor) {
                            tinyMCE.activeEditor.focus();
                            if (this.bMark) {
                                tinyMCE.activeEditor.selection.moveToBookmark(this.bMark);
                            }
                        }

                        this.updateContent(transport.responseText);
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            });
        }
    },

    updateContent: function(content) {
        if (this.wysiwygExists()) {
            this.getWysiwyg().execCommand("mceInsertContent", false, content);
        } else {
            var textarea = document.getElementById(this.widgetTargetId);
            updateElementAtCursor(textarea, content);
            varienGlobalEvents.fireEvent('tinymceChange');
        }
    },

    wysiwygExists: function() {
        return (typeof tinyMCE != 'undefined') && tinyMCE.get(this.widgetTargetId);
    },

    getWysiwyg: function() {
        return tinyMCE.activeEditor;
    },

    getWysiwygNode: function() {
        return tinyMCE.activeEditor.selection.getNode();
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

    // Chooser dialog window
    dialogWindow: null,

    // Chooser content for dialog window
    dialogContent: null,

    overlayShowEffectOptions: null,
    overlayHideEffectOptions: null,

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

    open: function() {
        $(this.getResponseContainerId()).show();
    },

    close: function() {
        $(this.getResponseContainerId()).hide();
        this.closeDialogWindow();
    },

    choose: function(event) {
        // Open dialog window with previously loaded dialog content
        if (this.dialogContent) {
            this.openDialogWindow(this.dialogContent);
            return;
        }
        // Show or hide chooser content if it was already loaded
        var responseContainerId = this.getResponseContainerId();

        // Otherwise load content from server
        new Ajax.Request(this.chooserUrl,
            {
                parameters: {element_value: this.getElementValue(), element_label: this.getElementLabelText()},
                onSuccess: function(transport) {
                    try {
                        widgetTools.onAjaxSuccess(transport);
                        this.dialogContent = widgetTools.getDivHtml(responseContainerId, transport.responseText);
                        this.openDialogWindow(this.dialogContent);
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            }
        );
    },

    openDialogWindow: function(content) {
        this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
        this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
        Windows.overlayShowEffectOptions = {duration:0};
        Windows.overlayHideEffectOptions = {duration:0};
        this.dialogWindow = Dialog.info(content, {
            draggable:true,
            resizable:true,
            closable:true,
            className:"magento",
            windowClassName:"popup-window",
            title:this.config.buttons.open,
            top:50,
            width:950,
            height:500,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:"widget-chooser",
            onClose: this.closeDialogWindow.bind(this)
        });
        content.evalScripts.bind(content).defer();
    },

    closeDialogWindow: function(dialogWindow) {
        if (!dialogWindow) {
            dialogWindow = this.dialogWindow;
        }
        if (dialogWindow) {
            dialogWindow.close();
            Windows.overlayShowEffectOptions = this.overlayShowEffectOptions;
            Windows.overlayHideEffectOptions = this.overlayHideEffectOptions;
        }
        this.dialogWindow = null;
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
