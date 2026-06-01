/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

function telephoneElem(fval, f1, f2, f3, f4) {
    this.valField = document.getElementById(fval);
    this.f1 = document.getElementById(f1);
    this.f2 = document.getElementById(f2);
    this.f3 = document.getElementById(f3);
    this.f4 = f4 ? document.getElementById(f4) : null;
    this.last = this.f4 || this.f3;

    this.eventKeyPress = this.keyPress.bind(this);
    this.eventKeyUp    = this.keyUp.bind(this);

    this.f1.addEventListener('keyup',    this.eventKeyUp);
    this.f2.addEventListener('keyup',    this.eventKeyUp);
    this.f3.addEventListener('keyup',    this.eventKeyUp);
    this.f1.addEventListener('keypress', this.eventKeyPress);
    this.f2.addEventListener('keypress', this.eventKeyPress);
    this.f3.addEventListener('keypress', this.eventKeyPress);

    if (this.f4) {
        this.f4.addEventListener('keyup',    this.eventKeyUp);
        this.f4.addEventListener('keypress', this.eventKeyPress);
    }

    this.loadValues();
}

telephoneElem.prototype = {
    keyPress: function(event) {
        // reserved for future key filtering
    },

    keyUp: function(event) {
        var element = event.target;
        var code = event.keyCode;
        var skipKeys = [9, 16, 8, 46, 37, 39]; // Tab, Shift, Backspace, Delete, Left, Right
        if (element !== this.last && skipKeys.indexOf(code) === -1) {
            var size = parseInt(element.getAttribute('size'), 10) || element.size;
            if (element.value.length === size) {
                var next = this.getNextElement(element);
                if (next) next.focus();
            }
        }
        this.setValField();
    },

    getNextElement: function(element) {
        if (element === this.last) return null;
        if (element === this.f1) return this.f2;
        if (element === this.f2) return this.f3;
        if (element === this.f3) return this.f4;
        return null;
    },

    setValField: function() {
        var val = '';
        if (this.f1.value) val += '(' + this.f1.value + ') ';
        if (this.f2.value) val += this.f2.value;
        if (this.f3.value) val += '-' + this.f3.value;
        if (this.f4) val += this.f4.value ? '-' + this.f4.value : '';
        this.valField.value = val;
    },

    loadValues: function() {
        var val = this.valField.value;
        if (val && val.length) {
            var re = /^[\(]?(\d{3})[\)]?[-|\s]?(\d{3})[-|\s](\d{4})[-|\s]?(\d{0,4})?$/;
            if (re.test(val)) {
                var arrVal = re.exec(val);
                this.f1.value = arrVal[1];
                this.f2.value = arrVal[2];
                this.f3.value = arrVal[3];
                if (this.f4 && arrVal[4]) {
                    this.f4.value = arrVal[4];
                }
            }
        }
    }
};
