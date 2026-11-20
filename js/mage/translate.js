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
function Translate() {
    this.initialize(...arguments);
}

Translate.prototype = {
    initialize: function(data) {
        // Null-prototype map: translation keys like "hasOwnProperty" or "__proto__"
        // stay plain data instead of touching Object.prototype.
        this.data = Object.create(null);
        if (data && typeof data === 'object') {
            const keys = Object.keys(data);
            for (let i = 0; i < keys.length; i++) {
                this.data[keys[i]] = data[keys[i]];
            }
        }
    },

    translate: function () {
        const text = arguments[0];
        if (Object.prototype.hasOwnProperty.call(this.data, text) && this.data[text]) {
            return this.data[text];
        }
        return text;
    },

    add: function () {
        if (arguments.length > 1) {
            this.data[arguments[0]] = arguments[1];
        } else if (typeof arguments[0] === 'object') {
            const obj = arguments[0];
            const keys = Object.keys(obj);
            for (let i = 0; i < keys.length; i++) {
                this.data[keys[i]] = obj[keys[i]];
            }
        }
    }
};
// The print layout loads this file before varien/js.js and the OAuth layouts
// drop js.js, so inline the Class.create hooks instead of calling Varien.classCompat.
Translate.superclass = null;
Translate.subclasses = [];
if (typeof Class !== 'undefined' && Class.Methods) { Object.assign(Translate, Class.Methods); }
