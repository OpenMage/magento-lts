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
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function toggleMenu(el, over)
{
    if (Element.childElements(el)) {
    var uL = Element.childElements(el)[1];
    var iS = true;
    }
    if (over) {
        Element.addClassName(el, 'over');
        
        if(iS){
            uL.addClassName('shown-sub');
        };
    }
    else {
        Element.removeClassName(el, 'over');
        if(iS){
            uL.removeClassName('shown-sub');
        };
    }
}

ieHover = function() {
    var items, iframe;
    items = $$('#nav ul', '.truncated_full_value .item-options', '.tool-tip');
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
