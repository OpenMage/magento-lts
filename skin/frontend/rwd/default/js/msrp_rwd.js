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
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

Catalog.Map.showHelp = Catalog.Map.showHelp.wrap(function (parent, event) {
    var helpBox = $('map-popup');
    var bodyNode = $$('body')[0];

    // Resolve calculation bug in parent so we can actually use these classes...
    if (helpBox && this != Catalog.Map && Catalog.Map.active != this.link) {
        parent(event);

        helpBox.removeClassName('map-popup-right');
        helpBox.removeClassName('map-popup-left');
        if (Element.getWidth(bodyNode) < event.pageX + (Element.getWidth(helpBox) / 2)) {
            helpBox.addClassName('map-popup-left');
        } else if (event.pageX - (Element.getWidth(helpBox) / 2) < 0) {
            helpBox.addClassName('map-popup-right');
        }
    } else {
        parent(event);
    }
});
