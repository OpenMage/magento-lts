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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if((typeof Prototype=='undefined') || (typeof Element == 'undefined') || (typeof Element.Methods=='undefined')) {
    throw("ext-tree.js requires the Prototype JavaScript framework ");
}

var jsRe = /ext-tree\.js(\?.*)?$/,
    origName = 'ext-tree.orig.js',
    fixName = 'fix-defer.js',
    currentPath = '/js/extjs/';

$$('head script[src]').findAll(function(s) {
    return s.src.match(jsRe);
}).each(function(s) {
    currentPath = s.src.replace(jsRe, '');
});

document.write('<script type="text/javascript" src="' + currentPath + origName + '"><\/script>');
document.write('<script type="text/javascript" src="' + currentPath + fixName + '"><\/script>');
