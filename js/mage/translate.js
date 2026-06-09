/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 *
 * @constructor
 * @param {Object} data - key/value translation map
 */
function Translate(data) {
    this.data = {};
    if (data && typeof data === 'object') {
        var keys = Object.keys(data);
        for (var i = 0; i < keys.length; i++) {
            this.data[keys[i]] = data[keys[i]];
        }
    }
}

Translate.prototype = {
    translate: function () {
        var text = arguments[0];
        if (this.data.hasOwnProperty(text)) {
            return this.data[text];
        }
        return text;
    },

    add: function () {
        if (arguments.length > 1) {
            this.data[arguments[0]] = arguments[1];
        } else if (typeof arguments[0] === 'object') {
            var obj = arguments[0];
            var keys = Object.keys(obj);
            for (var i = 0; i < keys.length; i++) {
                this.data[keys[i]] = obj[keys[i]];
            }
        }
    }
};
