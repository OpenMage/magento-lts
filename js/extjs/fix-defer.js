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
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

(function(){
    var eDefer = Function.prototype.defer;
    Function.prototype.defer = function() {
        var argLen = arguments.length;
        if (argLen==0 || (argLen==1 && arguments[0]==1)) {
            //common for Prototype Ajax requests
            return this.delay.curry(0.01).apply(this, arguments);
        }

        return eDefer.apply(this, arguments);
    }
})();