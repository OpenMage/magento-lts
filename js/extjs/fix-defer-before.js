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

/*@cc_on
// code only for IE when ExtJs overwrite "defer" function in PrototypeJs
(function(){
    var eDefer = Function.prototype.defer;
    Function.prototype.defer = function(a1, a2, a3, a4) {
        // do not use "call" or "apply", only direct function call !!!
        // for some reason in this case setTimeout with time < ~50ms run directly in function scope
        // and throw stack overflow exception
        eDefer(this, a1 || 50, a2, a3, a4);
    };
})();
@*/