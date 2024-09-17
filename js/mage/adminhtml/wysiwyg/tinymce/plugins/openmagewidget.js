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
 * @copyright   Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

tinymce.PluginManager.add('openmagewidget', (ed, url) => {
    return {
        init: function (editor) {
            var self = this;
            this.activePlaceholder = null;

            editor.addCommand('mceOpenmagewidget', function (img) {
                if (self.activePlaceholder) {
                    img = self.activePlaceholder;
                }
                var config = tinyMceEditors.get(tinymce.activeEditor.id).openmagePluginsOptions.get('openmagewidget');
                widgetTools.openDialog(
                    config.widget_window_url + 'widget_target_id/' + editor.getElement().id + '/'
                );
            });

            editor.ui.registry.addIcon(
                'openmagewidget',
                '<svg width="18" height="18" viewBox="0 0 122.88 121.92" xmlns="http://www.w3.org/2000/svg">' +
                '<path d="M6.6,121.92H47.51a6.56,6.56,0,0,0,2.83-.64,6.68,6.68,0,0,0,2.27-1.79,6.63,6.63,0,0,0,1.5-4.17V74.58A6.56,6.56,0,0,0,53.58,72,6.62,6.62,0,0,0,50,68.47,6.56,6.56,0,0,0,47.51,68H6.6a6.5,6.5,0,0,0-2.43.48,6.44,6.44,0,0,0-2.11,1.34A6.6,6.6,0,0,0,.55,72,6.3,6.3,0,0,0,0,74.58v40.74a6.54,6.54,0,0,0,.43,2.32,6.72,6.72,0,0,0,1.2,2l.26.27a6.88,6.88,0,0,0,2,1.39,6.71,6.71,0,0,0,2.73.6ZM59.3,28.44,86,1.77A6.19,6.19,0,0,1,88.22.34,6.24,6.24,0,0,1,90.87,0a6,6,0,0,1,3.69,1.74l26.55,26.55a6,6,0,0,1,1.33,2,6.13,6.13,0,0,1-1.33,6.58L94.45,63.58a6,6,0,0,1-1.9,1.27,5.92,5.92,0,0,1-2.24.5,6.11,6.11,0,0,1-2.41-.43,5.74,5.74,0,0,1-2.05-1.34L59.3,37a6.09,6.09,0,0,1-1.76-3.88V32.8a6.14,6.14,0,0,1,1.77-4.36ZM6.6,59.64H47.51a6.56,6.56,0,0,0,5.1-2.43,6.46,6.46,0,0,0,1.11-2,6.59,6.59,0,0,0,.39-2.21V12.31a6.61,6.61,0,0,0-.53-2.58A6.62,6.62,0,0,0,50,6.19a6.56,6.56,0,0,0-2.45-.48H6.6a6.5,6.5,0,0,0-2.43.48A6.44,6.44,0,0,0,2.06,7.53,6.6,6.6,0,0,0,.55,9.71,6.31,6.31,0,0,0,0,12.31V53.05a6.48,6.48,0,0,0,.43,2.31,6.6,6.6,0,0,0,1.2,2l.26.27a6.88,6.88,0,0,0,2,1.39,6.71,6.71,0,0,0,2.73.6Zm40.92-6.57H6.6l0,0V12.28c3.51,0,40.93,0,41,0,0,3.44,0,40.75,0,40.77Zm22.23,68.85h40.91a6.56,6.56,0,0,0,2.83-.64,6.68,6.68,0,0,0,2.27-1.79,6.63,6.63,0,0,0,1.5-4.17V74.58a6.56,6.56,0,0,0-.53-2.57,6.62,6.62,0,0,0-3.62-3.54,6.56,6.56,0,0,0-2.45-.48H69.75a6.75,6.75,0,0,0-4.54,1.82A6.6,6.6,0,0,0,63.7,72a6.3,6.3,0,0,0-.55,2.59v40.74a6.54,6.54,0,0,0,.43,2.32,6.72,6.72,0,0,0,1.2,2l.26.27a6.88,6.88,0,0,0,2,1.39,6.71,6.71,0,0,0,2.73.6Zm40.92-6.57H69.75l0,0,0-40.77c3.51,0,40.93,0,41,0,0,3.44,0,40.75,0,40.77Zm-63.15,0H6.6l0,0V74.56c3.51,0,40.93,0,41,0,0,3.44,0,40.75,0,40.77Z"/>' +
                '</svg>'
            );

            let onAction = function () {
                editor.execCommand('mceOpenmagewidget');
            }

            let onSetup = function (api) {
                // Add a node change handler, selects the button in the UI when a image is selected
                editor.on('NodeChange', function (e) {
                    if (api.setActive) api.setActive(false);
                    var n = e.target;
                    if (n.id && n.nodeName == 'IMG') {
                        var widgetCode = Base64.idDecode(n.id);
                        if (widgetCode.indexOf('{{widget') != -1) {
                            if (api.setActive) api.setActive(true);
                        }
                    }
                });
            }

            editor.ui.registry.addToggleButton('openmagewidget', {
                icon: 'openmagewidget',
                tooltip: Translator.translate('OpenMage Widget'),
                onAction: onAction,
                onSetup: onSetup
            });

            editor.ui.registry.addMenuItem('openmagewidget', {
                icon: 'openmagewidget',
                text: Translator.translate('OpenMage Widget'),
                onAction: onAction,
                onSetup: onSetup
            });

            // Add a widget placeholder image double click callback
            editor.on('dblClick', function (e) {
                var n = e.target;
                if (n.id && n.nodeName == 'IMG') {
                    var widgetCode = Base64.idDecode(n.id);
                    if (widgetCode.indexOf('{{widget') != -1) {
                        this.execCommand('mceOpenmagewidget', null);
                    }
                }
            });
        },

        getMetadata: function () {
            return {
                name: 'OpenMage Widget Manager Plugin',
                url: 'https://www.openmage.org'
            };
        }
    }
    
});
