/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
tinyMCE.addI18n({en:{
    magentowidget:
    {
        insert_widget : "Insert Widget"
    }
}});

/*
    TODO: Apply JStrim to reduce file size
*/

(function() {
    tinymce.create('tinymce.plugins.MagentowidgetPlugin', {
        /**
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addCommand('mceMagentowidget', function() {
                widgetTools.openDialog(ed.settings.magentowidget_url + 'widget_target_id/' + ed.getElement().id + '/');
            });

            // Register Widget plugin button
            ed.addButton('magentowidget', {
                title : 'magentowidget.insert_widget',
                cmd : 'mceMagentowidget',
                image : url + '/img/icon.gif'
            });

            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('magentowidget', false);
                if (n.id && n.nodeName == 'IMG') {
                    var widgetCode = Base64.idDecode(n.id);
                    if (widgetCode.indexOf('{{widget') != -1) {
                        cm.setActive('magentowidget', true);
                    }
                }
            });

            // Add a widget placeholder image double click callback
            ed.onDblClick.add(function(ed, e) {
                var n = e.target;
                if (n.id && n.nodeName == 'IMG') {
                    var widgetCode = Base64.idDecode(n.id);
                    if (widgetCode.indexOf('{{widget') != -1) {
                        ed.execCommand('mceMagentowidget');
                    }
                }
            });
        },

        getInfo : function() {
            return {
                longname : 'Magento Widget Manager Plugin for TinyMCE 3.x',
                author : 'Magento Core Team',
                authorurl : 'http://magentocommerce.com',
                infourl : 'https://www.openmage.org',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('magentowidget', tinymce.plugins.MagentowidgetPlugin);
})();
