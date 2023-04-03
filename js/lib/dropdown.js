/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
dropdown = function() {
    var ele = document.getElementById("nav").getElementsByTagName("LI");
    for (var i=0; i<ele.length; i++) {
        ele[i].onmouseover=function() {
            this.className+=" over";
        }
        ele[i].onmouseout=function() {
            this.className=this.className.replace(new RegExp(" over\\b"), "");
        }
    }
}
if (window.attachEvent) window.attachEvent("onload", dropdown);
