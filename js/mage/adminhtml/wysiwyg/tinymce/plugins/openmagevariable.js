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
    //TODO: the button doesn't show on the toolbar
    ed.ui.registry.addButton('openmagevariable', {
        text: 'Insert Variable',
        onAction: () => {
            //TODO: the code of this method needs to be converted to tinymce6
            var pluginSettings = ed.settings.magentoPluginsOptions.get('openmagevariable');
            openmagevariable.setEditor(ed);
            openmagevariable.loadChooser(pluginSettings.url, null);
        }
    });

    return {
        name: 'OpenMage Variable Manager Plugin for TinyMCE',
        url: 'https://www.openmage.org'
    };
});