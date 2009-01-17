/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
function toggleMenu(el, over)
{
	if (Element.childElements(el)) {
	var uL = Element.childElements(el)[1];
	var iS = true;
	}
    if (over) {
        Element.addClassName(el, 'over');
		
		if(iS){ uL.addClassName('shown-sub')};
    }
    else {
        Element.removeClassName(el, 'over');
		if(iS){ uL.removeClassName('shown-sub')};
    }
}

ieHover = function() {
	var items, iframe;
	items = $$('#nav ul', '.truncated_full_value .item-options');
	for (var j=0; j<items.length; j++) {
		iframe = document.createElement('IFRAME');
		iframe.src = BLANK_URL;
		iframe.scrolling = 'no';
		iframe.frameBorder = 0;
		iframe.className = 'hover-fix'
		iframe.style.width = items[j].offsetWidth+"px";
		iframe.style.height = items[j].offsetHeight+"px";
		items[j].insertBefore(iframe, items[j].firstChild);
	}
}

Event.observe(window, 'load', ieHover);