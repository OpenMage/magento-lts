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
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

ieHover = function() {
    var items, iframe;
    items = $$('#nav ul', '#nav div', '.truncated_full_value .item-options', '.tool-tip');
    $$('#checkout-step-payment', '.tool-tip').each(function(el) {
        el.show();
        el.setStyle({'visibility':'hidden'});
    });
    for (var j=0; j<items.length; j++) {
        iframe = document.createElement('IFRAME');
        iframe.src = BLANK_URL;
        iframe.scrolling = 'no';
        iframe.frameBorder = 0;
        iframe.className = 'hover-fix';
        iframe.style.width = items[j].offsetWidth+"px";
        iframe.style.height = items[j].offsetHeight+"px";
        items[j].insertBefore(iframe, items[j].firstChild);
    }
    $$('.tool-tip', '#checkout-step-payment').each(function(el) {
        el.hide();
        el.setStyle({'visibility':'visible'});
    });
};
Event.observe(window, 'load', ieHover);
