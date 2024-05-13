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
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

tinymce.PluginManager.add('openmagevariable', (ed, url) => {
    return {        
        init: function (editor) {
            editor.addCommand('openVariablesPopup', function (commandConfig) {
                var config = tinyMceEditors.get(tinymce.activeEditor.id).openmagePluginsOptions.get('openmagevariable');
                OpenmagevariablePlugin.setEditor(editor);
                OpenmagevariablePlugin.loadChooser(
                    config.url,
                    null,
                    tinymce.activeEditor.selection.getNode()
                );
            });

            editor.ui.registry.addIcon(
                'openmagevariable',
                '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">' +
                '<path d="M3.9298,0C2.37414,0,1.113,2.37414,1.113,3.9298V7.17695A1.47489,1.47489,0,0,1,.307,8.49146a0.56355,0.56355,0,0,0,0,1.017A1.475,1.475,0,0,1,1.113,10.823V14.0702C1.113,15.62585,2.37414,18,3.9298,18a0.56336,0.56336,0,0,0,0-1.12671c-0.9334,0-1.69007-1.86972-1.69007-2.80309V10.823A2.60153,2.60153,0,0,0,1.49423,9a2.60148,2.60148,0,0,0,.7455-1.82305V3.9298c0-.9334.75667-2.80309,1.69007-2.80309A0.56336,0.56336,0,0,0,3.9298,0ZM14.0702,0C15.62585,0,16.887,2.37414,16.887,3.9298V7.17695A1.47492,1.47492,0,0,0,17.693,8.49146a0.56354,0.56354,0,0,1,0,1.017A1.475,1.475,0,0,0,16.887,10.823V14.0702C16.887,15.62585,15.62585,18,14.0702,18a0.56336,0.56336,0,0,1,0-1.12671c0.93337,0,1.69007-1.86972,1.69007-2.80309V10.823A2.60114,2.60114,0,0,1,16.50581,9a2.60113,2.60113,0,0,1-.74554-1.82305V3.9298c0-.9334-0.7567-2.80309-1.69007-2.80309A0.56336,0.56336,0,1,1,14.0702,0ZM6.64169,4.72911a0.56336,0.56336,0,1,0-.91685.65489L8.30774,9l-2.5829,3.61607a0.56338,0.56338,0,0,0,.91685.655L9,9.96931,11.35843,13.271a0.56336,0.56336,0,1,0,.91681-0.655L9.69236,9l2.58287-3.616a0.56334,0.56334,0,1,0-.91681-0.65489L9,8.03081Z" />' +
                '</svg>'
            );

            let onAction = function () {
                editor.execCommand('openVariablesPopup');
            }

            editor.ui.registry.addToggleButton('openmagevariable', {
                icon: 'openmagevariable',
                tooltip: Translator.translate('OpenMage Variable'),
                onAction: onAction
            });

            editor.ui.registry.addMenuItem('openmagevariable', {
                icon: 'openmagevariable',
                text: Translator.translate('OpenMage Variable'),
                onAction: onAction
            });
        },

        getMetadata: function () {
            return {
                name: 'OpenMage Variable Manager Plugin',
                url: 'https://www.openmage.org'
            };
        }
    }
});
