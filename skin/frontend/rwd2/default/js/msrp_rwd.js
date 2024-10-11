/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    design
 * @package     rwd_default
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

Catalog.Map.showHelp = Catalog.Map.showHelp.wrap(function (parent, event) {
    var helpBox = document.getElementById('map-popup');
    var bodyNode = document.getElementsByTagName('body')[0];
    parent(event);
    
    if (helpBox && this != Catalog.Map && Catalog.Map.active != this.link) {
        helpBox.classList.remove('map-popup-right');
        helpBox.classList.remove('map-popup-left');
        if (Element.getWidth(bodyNode) < event.pageX + (Element.getWidth(helpBox) / 2)) {
            helpBox.classList.add('map-popup-left');
        } else if (event.pageX - (Element.getWidth(helpBox) / 2) < 0) {
            helpBox.classList.add('map-popup-right');
        }
    }
});
