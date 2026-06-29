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
 * @copyright   Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var widgetTools = {
    getDivHtml: function(id, html) {
        if (!html) html = '';
        return '<div id="' + id + '">' + html + '</div>';
    },

    // insertAdjacentHTML/innerHTML do not execute <script> tags. Re-create them
    // so inline init scripts (e.g. WysiwygWidget.chooser) run after AJAX insert.
    evalScripts: function(container) {
        if (!container) return;
        Array.from(container.querySelectorAll('script')).forEach(function(script) {
            var newScript = document.createElement('script');
            if (script.src) {
                newScript.src = script.src;
            } else {
                newScript.textContent = script.textContent;
            }
            document.head.appendChild(newScript).parentNode.removeChild(newScript);
        });
    },

    onAjaxSuccess: function(transport) {
        try {
            var response = JSON.parse(transport.responseText);
            if (response.error) {
                throw response;
            } else if (response.ajaxExpired && response.ajaxRedirect) {
                setLocation(response.ajaxRedirect);
            }
        } catch(e) {
            if (e.error) throw e;
        }
    },

    openDialog: function(widgetUrl) {
        if (document.getElementById('widget_window') && typeof(Windows) != 'undefined') {
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
            zIndex:9000,
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
            window.close();
        }
    }
};

var WysiwygWidget = {};
WysiwygWidget.Widget = function() {
    if (this.initialize) this.initialize.apply(this, arguments);
};
WysiwygWidget.Widget.prototype = {

    initialize: function(formEl, widgetEl, widgetOptionsEl, optionsSourceUrl, widgetTargetId) {
        document.getElementById(formEl).insertAdjacentHTML('beforeend', widgetTools.getDivHtml(widgetOptionsEl));
        this.formEl = formEl;
        this.widgetEl = document.getElementById(widgetEl);
        this.widgetOptionsEl = document.getElementById(widgetOptionsEl);
        this.optionsUrl = optionsSourceUrl;
        this.optionValues = {};
        this.widgetTargetId = widgetTargetId;
        if (typeof(tinyMCE) != "undefined" && tinyMCE.activeEditor) {
            this.bMark = tinyMCE.activeEditor.selection.getBookmark();
        }

        this.widgetEl.addEventListener("change", this.loadOptions.bind(this));

        this.initOptionValues();
    },

    getOptionsContainerId: function() {
        return this.widgetOptionsEl.id + '_' + this.widgetEl.value.replace(/\//g, '_');
    },

    switchOptionsContainer: function(containerId) {
        Array.from(document.querySelectorAll('#' + this.widgetOptionsEl.id + ' div[id^="' + this.widgetOptionsEl.id + '"]')).forEach(function(e) {
            this.disableOptionsContainer(e.id);
        }.bind(this));
        if(containerId != undefined) {
            this.enableOptionsContainer(containerId);
        }
        this._showWidgetDescription();
    },

    enableOptionsContainer: function(containerId) {
        Array.from(document.querySelectorAll('#' + containerId + ' .widget-option')).forEach(function(e) {
            e.classList.remove('skip-submit');
            if (e.classList.contains('obligatory')) {
                e.classList.remove('obligatory');
                e.classList.add('required-entry');
            }
        });
        document.getElementById(containerId).classList.remove('no-display');
    },

    disableOptionsContainer: function(containerId) {
        var container = document.getElementById(containerId);
        if (container.classList.contains('no-display')) {
            return;
        }
        Array.from(document.querySelectorAll('#' + containerId + ' .widget-option')).forEach(function(e) {
            if (!e.classList.contains('skip-submit')) {
                e.classList.add('skip-submit');
            }
            if (e.classList.contains('required-entry')) {
                e.classList.remove('required-entry');
                e.classList.add('obligatory');
            }
        });
        container.classList.add('no-display');
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
                this.optionValues = {};
                var self = this;
                widgetCode.replace(/([a-z0-9_]+)\s*=\s*"([^"]+)"/gi, function(match, key, val) {
                    if (key == 'type') {
                        self.widgetEl.value = val;
                    } else {
                        self.optionValues[key] = val;
                    }
                });

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
        if (document.getElementById(optionsContainerId) !== null) {
            this.switchOptionsContainer(optionsContainerId);
            return;
        }

        this._showWidgetDescription();

        var params = {widget_type: this.widgetEl.value, values: this.optionValues};
        new Ajax.Request(this.optionsUrl,
            {
                parameters: {widget: JSON.stringify(params)},
                onSuccess: function(transport) {
                    try {
                        widgetTools.onAjaxSuccess(transport);
                        this.switchOptionsContainer();
                        if (document.getElementById(optionsContainerId) === null) {
                            this.widgetOptionsEl.insertAdjacentHTML('beforeend', widgetTools.getDivHtml(optionsContainerId, transport.responseText));
                            widgetTools.evalScripts(document.getElementById(optionsContainerId));
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
        var noteCnt = this.widgetEl.nextElementSibling ? this.widgetEl.nextElementSibling.querySelector('small') : null;
        var descrCnt = document.getElementById('widget-description-' + this.widgetEl.selectedIndex);
        if (noteCnt !== null) {
            noteCnt.innerHTML = descrCnt ? descrCnt.innerHTML : '';
        }
    },

    insertWidget: function() {
        widgetOptionsForm = new varienForm(this.formEl);
        if(widgetOptionsForm.validator && widgetOptionsForm.validator.validate() || !widgetOptionsForm.validator){
            var formElements = [];
            var i = 0;
            document.getElementById(this.formEl).querySelectorAll('input, select, textarea, button').forEach(function(e) {
                if (!e.classList.contains('skip-submit')) {
                    formElements[i] = e;
                    i++;
                }
            });

            // Add as_is flag to parameters if wysiwyg editor doesn't exist
            var params = Form.serializeElements(formElements);
            if (!this.wysiwygExists()) {
                params = params + '&as_is=1';
            }

            new Ajax.Request(document.getElementById(this.formEl).action,
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
};

WysiwygWidget.chooser = function() {
    if (this.initialize) this.initialize.apply(this, arguments);
};
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
        return document.getElementById(this.chooserId + 'control');
    },

    getElement: function() {
        return document.getElementById(this.chooserId + 'value');
    },

    getElementLabel: function() {
        return document.getElementById(this.chooserId + 'label');
    },

    open: function() {
        document.getElementById(this.getResponseContainerId()).style.display = '';
    },

    close: function() {
        document.getElementById(this.getResponseContainerId()).style.display = 'none';
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
            zIndex:9000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:"widget-chooser",
            onClose: this.closeDialogWindow.bind(this)
        });
        setTimeout(function() { content.evalScripts(); }, 0);
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
};
