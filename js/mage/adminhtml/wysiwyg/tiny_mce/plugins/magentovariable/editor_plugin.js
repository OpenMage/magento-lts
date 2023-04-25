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

tinymce.PluginManager.add('OpenMageVariablePlugin', (ed, url) => {
    ed.addCommand('mceMagentovariable', function() {
        var pluginSettings = ed.settings.magentoPluginsOptions.get('magentovariable');
        MagentovariablePlugin.setEditor(ed);
        MagentovariablePlugin.loadChooser(pluginSettings.url, null);
    });

    // Register Widget plugin button
    ed.addButton('magentovariable', {
        title : 'magentovariable.insert_variable',
        cmd : 'mceMagentovariable',
        image : url + '/img/icon.gif'
    });

    return {
        name: 'OpenMage Variable Manager Plugin for TinyMCE',
        url: 'https://www.openmage.org'
    };
});