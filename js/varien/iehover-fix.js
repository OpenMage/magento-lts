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
	var ieULs, iframe, li;
	if($('nav')){
	    ieULs = $('nav').getElementsByTagName('ul');
    	for (var j=0; j<ieULs.length; j++) {
    		iframe = document.createElement('IFRAME');
    		iframe.src = BLANK_URL;
    		iframe.scrolling = 'no';
    		iframe.frameBorder = 0;
    		iframe.style.width = ieULs[j].offsetWidth+"px";
    		iframe.style.height = ieULs[j].offsetHeight+"px";
    		ieULs[j].insertBefore(iframe, ieULs[j].firstChild);
    		ieULs[j].style.zIndex="1";
    	}
	}
	var truncated=$$('.truncated_full_value .item-options');
	for (var i=0; i<truncated.length; i++) {
		    iframe = document.createElement('IFRAME');
    		iframe.src = BLANK_URL;
    		iframe.scrolling = 'no';
    		iframe.frameBorder = 0;
    		iframe.style.width = truncated[i].offsetWidth+"px";
    		iframe.style.height = truncated[i].offsetHeight+"px";
    		truncated[i].insertBefore(iframe, truncated[i].firstChild);
    		truncated[i].style.zIndex="1";
	}
}

Event.observe(window, 'load', ieHover);