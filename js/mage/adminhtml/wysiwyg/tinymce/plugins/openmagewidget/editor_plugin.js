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

tinymce.PluginManager.add('openmagewidget', (ed, url) => {
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

    return {
        name: 'OpenMage Widget Manager Plugin for TinyMCE',
        url: 'https://www.openmage.org'
    };
});