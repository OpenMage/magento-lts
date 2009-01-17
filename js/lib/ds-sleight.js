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
// Universal transparent-PNG enabler for MSIE/Win 5.5+
// http://dsandler.org
// From original code: http://www.youngpup.net/?request=/snippets/sleight.xml
// and background-image code: http://www.allinthehead.com/retro/69
// also:
//  * use sizingMethod=crop to avoid scaling PNGs (who would do such a thing?)
//  * only do this once, to make it compatible with CSS rollovers

if (navigator.platform == "Win32" && navigator.appName == "Microsoft Internet Explorer" && window.attachEvent) {
	window.attachEvent("onload", enableAlphaImages);
}

function enableAlphaImages(){
	var rslt = navigator.appVersion.match(/MSIE (\d+\.\d+)/, '');
	var itsAllGood = (rslt != null && Number(rslt[1]) >= 5.5);
	if (itsAllGood) {
		for (var i=0; i<document.all.length; i++){
			var obj = document.all[i];
			var bg = obj.currentStyle.backgroundImage;
			var img = document.images[i];
			if (bg && bg.match(/\.png/i) != null) {
				var img = bg.substring(5,bg.length-2);
				var offset = obj.style["background-position"];
				obj.style.filter =
				"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+img+"', sizingMethod='crop')";
				obj.style.backgroundImage = "url('"+BLANK_IMG+"')";
				obj.style["background-position"] = offset; // reapply
			} else if (img && img.src.match(/\.png$/i) != null) {
				var src = img.src;
				img.style.width = img.width + "px";
				img.style.height = img.height + "px";
				img.style.filter =
				"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+src+"', sizingMethod='crop')"
				img.src = BLANK_IMG;
			}
		}
	}
}